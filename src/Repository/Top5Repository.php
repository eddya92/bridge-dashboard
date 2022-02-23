<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface Top5Repository{
	public const TAG_TOP5 = 'top5';

	public function getTop5(string $anno, string $mese) : ?Generator;
}
