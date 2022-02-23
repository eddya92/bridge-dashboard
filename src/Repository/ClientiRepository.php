<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface ClientiRepository{
	public const TAG_CLIENTI = 'clienti';

	public function getClienti(string $_locale, string $ricercaGenerica, string $dataDal, string $dataAl) : ?Generator;
}
