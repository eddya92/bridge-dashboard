<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface KitsRepository{
	public const TAG_KITS = 'kits';

	public function getKits(string $_locale) : ?Generator;
}
