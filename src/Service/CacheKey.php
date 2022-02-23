<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\RestApi\RestApiConnection;
use Symfony\Component\Security\Core\User\UserInterface;
use function debug_backtrace;
use function end;
use function hash;
use function serialize;
use function sprintf;
use const DEBUG_BACKTRACE_PROVIDE_OBJECT;

final class CacheKey{
	public static function fromTrace() : string{
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
		$trace = end($backtrace);

		return hash('tiger128,3', sprintf('%s::%s(%s)', $trace['class'], $trace['function'], serialize($trace['args'])));
	}

	public static function keyForAuthenticatedRestApiCall(UserInterface $user, RestApiConnection $connection) : string{
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);

		$i = 0;
		do{
			$trace = $backtrace[$i];
			$object = $trace['object'] ?? null;
			$i++;
		}while(!$object instanceof AuthenticatedRepository);
		$trace = $backtrace[$i++];

		return hash(
			'tiger128,3',
			sprintf(
				'%s::%s(%s,%s,%s)',
				$trace['class'],
				$trace['function'],
				$user->getUserIdentifier(),
				$connection->signature(),
				serialize($trace['args'])
			)
		);
	}

	public static function tagForAuthenticatedRestApiCall(string $prefix, UserInterface $user, RestApiConnection $connection) : string{
		return hash(
			'tiger128,3',
			sprintf(
				'%s(%s,%s)',
				$prefix,
				$user->getUserIdentifier(),
				$connection->signature()
			)
		);
	}
}
