<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\DatiFiscaliViewModel;

interface DatiFiscaliRepository{
	public const TAG_DATI_FISCALI = 'datiFiscali';

	/**
	 * Api che ritorna i dati fiscali di un cliente (codice fiscale, p.iva, pec , codice univoco)
	 *
	 * @param string $_locale
	 *
	 * @return DatiFiscaliViewModel
	 */
	public function getDatiFiscali(string $_locale) : DatiFiscaliViewModel;

	/**
	 *  Api che aggiorna i dati fiscali di un cliente
	 *
	 * @param string $codiceFiscale
	 * @param string $PIVA
	 * @param string $PEC
	 * @param string $codiceUnivoco
	 *
	 * @return array
	 */
	public function aggiornaDatiFiscali(string $codiceFiscale, string $PIVA, string $PEC, string $codiceUnivoco) : array;
}