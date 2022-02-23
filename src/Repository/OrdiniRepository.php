<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\DettaglioOrdineViewModel;
use Generator;

interface OrdiniRepository{
	public const TAG_ORDINI = 'ordini';

	public function getOrdini(string $sottoposti, string $clienti, string $esito, string $data_dal, string $data_al, string $tipolgia_ordine) : ?Generator;

	public function getDettaglioOrdine(int $id) : ?DettaglioOrdineViewModel;
}
