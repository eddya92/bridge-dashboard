<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\Top5UserViewModel;
use Generator;

interface OneRepository{
	public function get(string $userId, string $oneParam, string $anotherParam) : string;

	/**
	 * @return Generator<int, string>
	 */
	public function listOfSomething(string $userId, string $firstParm) : Generator;

	/**
	 * @return Generator<int, Top5UserViewModel>
	 */
	public function listOfAnotherThing(string $param) : Generator;

	/**
	 * @return Generator<int, Top5UserViewModel>
	 */
	public function listOfCombinedThings(string $param) : Generator;
}
