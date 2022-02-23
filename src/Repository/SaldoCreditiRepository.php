<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\SaldoViewModel;

interface SaldoCreditiRepository{
	public const TAG_SALDO = 'saldo';

	public function getSaldo() : ?SaldoViewModel;
}
