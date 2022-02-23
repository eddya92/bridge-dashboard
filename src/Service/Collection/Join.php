<?php
declare(strict_types=1);

namespace App\Service\Collection;

use loophp\collection\Contract\Collection;
use function array_merge;

/**
 * @immutable
 *
 * @template TKey
 * @template T
 */
final class Join{
	public static function of(
		Collection $leftCollection,
		Collection $rightCollection,
		string     $leftKey,
		string     $rightKey
	) : Collection{
		return $leftCollection
			->filter(
				static function(array $left) use ($rightCollection, $leftKey, $rightKey) : bool{
					$filtered = $rightCollection->filter(
						fn(array $right) : bool => $left[$leftKey] === $right[$rightKey]
					)
						->normalize()
						->current();

					return null !== $filtered;
				}
			)
			->map(
				static function(array $left) use ($rightCollection, $leftKey, $rightKey) : array{
					return array_merge(
						$left,
						$rightCollection->filter(fn(array $right) : bool => $left[$leftKey] === $right[$rightKey])
							->current()
					);
				}
			)
			->normalize();
	}
}
