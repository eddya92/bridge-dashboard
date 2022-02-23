<?php
declare(strict_types=1);

namespace App\Security;

use App\Repository\RestApi\RemoteAuthenticator;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PreAuthenticatedUserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use function array_merge;
use function gettype;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;
use function strlen;
use function trim;

final class CustomFormLoginAuthenticator extends AbstractLoginFormAuthenticator{
	private array $options;

	public function __construct(
		private HttpUtils                             $httpUtils,
		private UserProviderInterface                 $userProvider,
		private RemoteAuthenticator                   $remoteAuthenticator,
		private AuthenticationSuccessHandlerInterface $successHandler,
		private AuthenticationFailureHandlerInterface $failureHandler,
		array                                         $options,
		private                                       $hostName,
		private                                       $secretKey,
	){
		$this->options = array_merge(
			[
				'username_parameter' => '_username',
				'password_parameter' => '_password',
				'check_path'         => '/login_check',
				'post_only'          => true,
				'enable_csrf'        => false,
				'csrf_parameter'     => '_csrf_token',
				'csrf_token_id'      => 'authenticate',
			], $options
		);
	}

	public function supports(Request $request) : bool{
		return ($this->options['post_only'] ? $request->isMethod('POST') : true)
			&& $this->httpUtils->checkRequestPath($request, $this->options['check_path']);
	}

	public function authenticate(Request $request) : PassportInterface{
		$recaptcha = new ReCaptcha($this->secretKey);
		$resp = $recaptcha->setExpectedHostname($this->hostName)
			->verify($request->get('g-recaptcha-response'));

		$credentials = $this->getCredentials($request);
		if($resp->isSuccess()){
			// Verified!
			$token = $this->remoteAuthenticator->authenticate($credentials['username'], $credentials['password']);
			$passport = new SelfValidatingPassport(new UserBadge($token->toString()), [new RememberMeBadge(), new PreAuthenticatedUserBadge()]);
		}else{
			throw new AuthenticationException('Validazione ReCaptcha fallita.');
		}

		if($this->options['enable_csrf']){
			$passport->addBadge(new CsrfTokenBadge($this->options['csrf_token_id'], $credentials['csrf_token']));
		}

		if($this->userProvider instanceof PasswordUpgraderInterface){
			$passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));
		}

		return $passport;
	}

	private function getCredentials(Request $request) : array{
		$credentials = [];
		$credentials['csrf_token'] = $request->get($this->options['csrf_parameter']);

		if($this->options['post_only']){
			$credentials['username'] = $request->request->get($this->options['username_parameter']);
			$credentials['password'] = $request->request->get($this->options['password_parameter'], '');
		}else{
			$credentials['username'] = $request->get($this->options['username_parameter']);
			$credentials['password'] = $request->get($this->options['password_parameter'], '');
		}

		if(!is_string($credentials['username'])
			&& (!is_object($credentials['username'])
				|| !method_exists($credentials['username'], '__toString'))){
			throw new BadRequestHttpException(sprintf('The key "%s" must be a string, "%s" given.', $this->options['username_parameter'], gettype($credentials['username'])));
		}

		$credentials['username'] = trim($credentials['username']);
		$credentials['password'] = trim($credentials['password']);

		if(strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH){
			throw new BadCredentialsException('Invalid username.');
		}

		$request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

		return $credentials;
	}

	/**
	 * @param Passport $passport
	 */
	public function createAuthenticatedToken(PassportInterface $passport, string $firewallName) : TokenInterface{
		return new PreAuthenticatedToken($passport->getUser(), null, $firewallName, $passport->getUser()->getRoles());
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName) : ?Response{
		return $this->successHandler->onAuthenticationSuccess($request, $token);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception) : Response{

		return $this->failureHandler->onAuthenticationFailure($request, $exception);
	}

	public function setHttpKernel(HttpKernelInterface $httpKernel) : void{
		$this->httpKernel = $httpKernel;
	}

	public function start(Request $request, AuthenticationException $authException = null) : Response{
		if(!$this->options['use_forward']){
			return parent::start($request, $authException);
		}

		$subRequest = $this->httpUtils->createRequest($request, $this->options['login_path']);
		$response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
		if(200 === $response->getStatusCode()){
			$response->setStatusCode(401);
		}

		return $response;
	}

	protected function getLoginUrl(Request $request) : string{
		return $this->httpUtils->generateUri($request, $this->options['login_path']);
	}
}
