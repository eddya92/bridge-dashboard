<?php
declare(strict_types=1);

namespace App\Repository\RestApi;

interface RemoteAuthenticator
{
    public function authenticate(string $username, string $password): Token;
}
