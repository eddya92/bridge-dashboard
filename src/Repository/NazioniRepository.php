<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface NazioniRepository{

	public function getNazioni() : ?Generator;

	public function getAgreements(string $idNazione) : ?Generator;

	public function getUserAgreements(string $idNazione) : ?Generator;
}
