<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\DatiFiscaliViewModel;
use Generator;

interface DatiFiscaliRepository{
	public const TAG_DATI_FISCALI = 'datiFiscali';

	public function getDatiFiscali(string $_locale) : ?DatiFiscaliViewModel;

	public function aggiornaDatiFiscali(
		string $codiceFiscale,
		string $PIVA,
		string $PEC,
		string $codiceUnivoco,
	) : array;
}