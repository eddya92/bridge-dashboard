<?php
declare(strict_types=1);

namespace App\Service;

use JsonException;
use function json_decode;
use function json_encode;
use const JSON_BIGINT_AS_STRING;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class Json{
	/**
	 * @param mixed $value
	 *
	 * @throws JsonException
	 */
	public static function encode($value) : string{
		$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR;

		return json_encode($value, $flags);
	}

	/**
	 * @param string $json
	 *
	 * @return mixed
	 *
	 * @throws JsonException
	 */
	public static function decode(string $json){
		$flags = JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR;

		return json_decode($json, true, 512, $flags);
	}
}
