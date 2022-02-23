<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface TotaliRepository{
	public const TAG_TOTALI = 'totali';

	public function getTotali() : ?Generator;
}
