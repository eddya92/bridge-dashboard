<?php

namespace App\Security;

use App\Repository\RestApi\RemoteAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PreAuthenticatedUserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class SSOAuthenticator extends AbstractAuthenticator{
	public function __construct(
		private RemoteAuthenticator                   $remoteAuthenticator,
		private UserProviderInterface                 $userProvider,
		private AuthenticationSuccessHandlerInterface $successHandler,
		private AuthenticationFailureHandlerInterface $failureHandler,
		array                                         $options,
	){
	}

	public function supports(Request $request) : ?bool{
		return $request->query->has('token_sso');
	}

	public function authenticate(Request $request) : Passport{
		$token_sso = $request->query->get('token_sso', '');
		$token = $this->remoteAuthenticator->authenticateByTokenSSO($token_sso);

		return new SelfValidatingPassport(new UserBadge($token->toString()), [new PreAuthenticatedUserBadge()]);
	}

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