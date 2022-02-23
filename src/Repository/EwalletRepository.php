<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface EwalletRepository{
	public const TAG_EWALLET = 'ewallet';

	public function getMovimenti() : ?Generator;

	public function getPagamenti() : ?Generator;
}
