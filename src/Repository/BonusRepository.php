<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\BonusViewModel;

interface BonusRepository{
	public const TAG_BONUS = 'bonus';

	/**
	 * @param int    $anno
	 * @param string $locale
	 *
	 * @return iterable<BonusViewModel>|null
	 */
	public function listaBonus(int $anno,string $locale) : iterable;
}
