<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface VenditeRepository{
	public const TAG_VENDITE = 'vendite';

	public function vendite($dato, $anno, $mese = '') : Generator;
}
