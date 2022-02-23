<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1\Security;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\RestApi\Token;
use App\Security\User;
use App\Service\CacheKey;
use App\Service\Json;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use function is_subclass_of;
use function sprintf;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $authRefreshTtl
	){
	}

	/**
	 * @deprecated since Symfony 5.3, loadUserByIdentifier() is used instead
	 */
	public function loadUserByUsername($username) : UserInterface{
		return $this->loadUserByIdentifier($username);
	}

	/**
	 * Symfony calls this method if you use features like switch_user
	 * or remember_me.
	 *
	 * If you're not using these features, you do not need to implement
	 * this method.
	 *
	 * @throws UserNotFoundException if the user is not found
	 */
	public function loadUserByIdentifier($identifier) : UserInterface{
		$cacheKey = CacheKey::fromTrace();

		$cached = $this->cache->get(
			$cacheKey,
			function(ItemInterface $item) use ($identifier) : string{
				try{
					$response = $this->connection
						->withAuthentication(new Token($identifier))
						->client()
						->get('/db-v1/authenticate/user-by-token?token=' . $identifier);

					$item->expiresAfter($this->authRefreshTtl);

					return (string) $response->getBody();
				}catch(GuzzleException $exception){
					$exception->getCode();

					$item->expiresAt(null);

					return '{}';
				}
			}
		);

		$userData = Json::decode($cached)['data'] ?? [];

		if([] === $userData){
			$exception = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
			$exception->setUserIdentifier($identifier);

			throw $exception;
		}

		return new User(
			$userData['token'],
			$userData['roles'] ?? [],
			$userData['superiore'] ?? '',
			$userData['codice'] ?? '',
			$userData['nominativo'] ?? '',
			$userData['qualifica'] ?? '',
			$userData['id_ruolo'] ?? '',
			$userData['foto'] ?? '',
			$userData['articoli_nel_carrello'] ?? 0,
		);
	}

	/**
	 * Refreshes the user after being reloaded from the session.
	 *
	 * When a user is logged in, at the beginning of each request, the
	 * User object is loaded from the session and then this method is
	 * called. Your job is to make sure the user's data is still fresh by,
	 * for example, re-querying for fresh User data.
	 *
	 * If your firewall is "stateless: true" (for a pure API), this
	 * method is not called.
	 *
	 * @return UserInterface
	 */
	public function refreshUser(UserInterface $user){
		if(!$user instanceof User){
			throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
		}

		return $this->loadUserByIdentifier($user->getUserIdentifier());
	}

	/**
	 * Tells Symfony to use this provider for this User class.
	 */
	public function supportsClass($class){
		return User::class === $class || is_subclass_of($class, User::class);
	}

	/**
	 * Upgrades the hashed password of a user, typically for using a better hash algorithm.
	 */
	public function upgradePassword(UserInterface $user, string $newHashedPassword) : void{
		$this->connection->client()
			->put('/user', ['form_params' => ['password' => $newHashedPassword]]);

		$user->setPassword($newHashedPassword);
	}
}
