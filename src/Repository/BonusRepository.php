<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface BonusRepository{
	public const TAG_BONUS = 'bonus';

	public function listaBonus(int $anno,string $locale) : ?Generator;
}
