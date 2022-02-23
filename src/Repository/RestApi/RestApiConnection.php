<?php
declare(strict_types=1);

namespace App\Repository\RestApi;

use GuzzleHttp\Client;

interface RestApiConnection
{
    public function withAuthentication(Token $token): self;

    public function client(): Client;

    public function signature(): string;
}
