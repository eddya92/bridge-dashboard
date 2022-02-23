<?php
declare(strict_types=1);

namespace App\Repository\RestApi;

final class Token
{
    public function __construct(private string $key)
    {
    }

    public function toString(): string
    {
        return $this->key;
    }
}
