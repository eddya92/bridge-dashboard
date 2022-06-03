<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\ClientiViewModel;

interface ClientiRepository{
	public const TAG_CLIENTI = 'clienti';

	/**
	 * APi che ritorna l'elenco dei clienti
	 *
	 * @param string $_locale
	 * @param string $ricercaGenerica
	 * @param string $dataDal
	 * @param string $dataAl
	 *
	 * @return iterable<ClientiViewModel>
	 */
	public function getClienti(string $_locale, string $ricercaGenerica, string $dataDal, string $dataAl) : iterable;
}
