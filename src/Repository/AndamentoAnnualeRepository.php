<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface AndamentoAnnualeRepository{
	public const TAG_ANDAMENTO_ANNUALE = 'andamentoAnnuale';

	/**
	 * Andamento mese per mese di un utente(Chiamata che va a riempire il datatable nella pagina della carriera)
	 *
	 * @param string $locale
	 * @param string $filtroColonnaOrdinamento
	 * @param string $filtroDirezioneOrdinamento
	 *
	 * @return Generator
	 */
	public function getCarrieraAnnuale(string $locale, string $filtroColonnaOrdinamento, string $filtroDirezioneOrdinamento, string $pag, string $items) : Generator;
}
