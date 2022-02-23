<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface CarrieraPersonaleRepository{
	public const TAG_CARRIERA_PERSONALE = 'carrieraPersonale';

	public function getCarriera(string $_locale) : ?Generator;
}
