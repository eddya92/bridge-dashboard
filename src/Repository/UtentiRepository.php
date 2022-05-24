<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\UtenteViewModel;
use Generator;

interface UtentiRepository{
	public const TAG_UTENTI = 'utenti';

	/**
	 * Ritorna tutti gli utenti che soddisfano (in Nome, Cognome, Codice, etc...) la stringa di ricerca $search
	 *
	 * @param string $search
	 *
	 * @return iterable<UtenteViewModel>
	 */
	public function allOfSearch(string $search = '') : iterable;
}
