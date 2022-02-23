<?php
declare(strict_types=1);

namespace App\Repository\RestApi;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\Attribute\Required;

interface AuthenticatedRepository
{
    #[Required]
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void;

    #[Required]
    public function setAppKey(AppKey $appKey): void;

    #[Required]
    public function setConnection(RestApiConnection $connection): void;
}
