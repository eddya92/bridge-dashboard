<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface AndamentoAnnualeRepository{
	public const TAG_ANDAMENTO_ANNUALE = 'andamentoAnnuale';

	public function getCarrieraAnnuale(string $locale, string $filtroColonnaOrdinamento, string $filtroDirezioneOrdinamento) : ?Generator;
}
