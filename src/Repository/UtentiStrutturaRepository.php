<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface UtentiStrutturaRepository{
	public const TAG_UTENTI_STRUTTURA = 'utentiStruttura';

	public function getUtentiStruttura(string $filtroGruppoDi, string $filtroNominativo, string $filtroEmail, string $filtroCellulare, string $filtroPeriodo, string $filtroDiretti) : ?Generator;
}
