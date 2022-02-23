<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1\Security;

use App\Repository\RestApi\RemoteAuthenticator;
use App\Repository\RestApi\RestApiConnection;
use App\Repository\RestApi\Token;
use App\Service\Json;
use Exception;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Throwable;
use function sprintf;

class Authenticator implements RemoteAuthenticator{
	public function __construct(private RestApiConnection $connection){
	}

	public function authenticate(string $username, string $password) : Token{
		try{
			$response = $this->connection
				->client()
				->post('/db-v1/authenticate/user', [
					'form_params' => [
						'username' => $username,
						'password' => $password,
					],
				]);

			if($response->getStatusCode() != 200){
				throw new Exception('Autenticazione fallita.', 'error');
			}

			$userData = Json::decode((string) $response->getBody())['data'] ?? [];

			$token = $userData['token'] ?? '';

			if('' === $token){
				throw new BadCredentialsException();
			}

			return new Token($token);
		}catch(Throwable $exception){
			if(404 === $exception->getCode()){
				$notFoundException = new UserNotFoundException(sprintf('User "%s" not found.', $username));
				$notFoundException->setUserIdentifier($username);

				throw $notFoundException;
			}
			if(403 === $exception->getCode()){
				throw new BadCredentialsException();
			}

			if(400 === $exception->getCode()){
				$notFoundException = new UserNotFoundException(sprintf('User "%s" not found.', $username));
				$notFoundException->setUserIdentifier($username);

				throw $notFoundException;
			}
			if(403 === $exception->getCode()){
				throw new BadCredentialsException();
			}

			throw new AuthenticationServiceException();
		}
	}
}
