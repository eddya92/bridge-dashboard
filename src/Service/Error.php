<?php
declare(strict_types=1);

namespace App\Service;

final class Error{
	public static function format(string $message) : string{
		if(str_contains($message, '"error_msg":"')){
			$message = substr($message, strpos($message, '"error_msg":"') + 13);
			$message = substr($message, 0, strrpos($message, '"}'));
			$message = str_replace('\u', 'u', $message);
			$message = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $message);
			$message = html_entity_decode($message);
		}

		return $message;
	}
}