<?php
declare(strict_types=1);

namespace App\Repository\RestApi;

use InvalidArgumentException;
use function trim;

final class AppKey
{
    public function __construct(private string $key)
    {
        $key = trim($key);

        if ('' === $key) {
            throw new InvalidArgumentException('App key can\'t be empty.');
        }
    }

    public function toString(): string
    {
        return $this->key;
    }
}
