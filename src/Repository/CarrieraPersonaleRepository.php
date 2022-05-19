<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface CarrieraPersonaleRepository{
	public const TAG_CARRIERA_PERSONALE = 'carrieraPersonale';

	public function getQualifiche(string $_locale) : ?Generator;

	/**
	 * Cosa cippa fa!
	 *
	 * @param string $_locale
	 *
	 * @return Generator|null
	 */
	public function infoProssimoRank(string $_locale) : ?Generator;

	public function confermaQualifica() : ?Generator;
}
