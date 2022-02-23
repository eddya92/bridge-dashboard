<?php
declare(strict_types=1);

namespace App\Repository\RestApi;

use App\Service\CacheKey;
use BadMethodCallException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait AuthenticatedConnectionCapability
{
    private TokenStorageInterface $tokenStorage;
    private AppKey $appKey;
    private RestApiConnection $connection;

    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setAppKey(AppKey $appKey): void
    {
        $this->appKey = $appKey;
    }

    public function setConnection(RestApiConnection $connection): void
    {
        $this->connection = $connection;
    }

    private function authenticatedCacheKey(): string
    {
        return CacheKey::keyForAuthenticatedRestApiCall($this->user(), $this->restApiConnection());
    }

    private function authenticatedCacheTag(string $prefix): string
    {
        return CacheKey::tagForAuthenticatedRestApiCall($prefix, $this->user(), $this->restApiConnection());
    }

    private function user(): UserInterface
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            throw new BadMethodCallException('This method should not have been called here, because the user should have already been recognized as unauthenticated.');
        }

        return $token->getUser();
    }

    private function appKey(): AppKey
    {
        return $this->appKey;
    }

    private function restApiConnection(): RestApiConnection
    {
        return $this->connection;
    }

    private function authenticationToken(): Token
    {
        return new Token($this->user()->getUserIdentifier());
    }
}
