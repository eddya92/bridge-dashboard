<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface VenditeRepository{
	public const TAG_VENDITE = 'vendite';

	public function vendite($utenza, $dato, $anno, $mese = '') : Generator;
}
