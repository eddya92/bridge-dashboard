<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface FiltriRepository{
	public const TAG_FILTRI_CATEGORIE = 'filtriCategorie';
	public const TAG_FILTRI_TIPO_ORDINE = 'filtriTipoOrdine';
	public const TAG_FILTRI_ESITO = 'filtriEsito';

	public function getCategorieFiltri(string $_locale) : ?Generator;

	public function getFiltriTipoOrdine(string $_locale) : ?Generator;

	public function getFiltriEsito(string $_locale) : ?Generator;
}
