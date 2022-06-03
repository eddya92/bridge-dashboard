<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\ContattiViewModel;

interface ContattiRepository{
	public const TAG_CONTATTI = 'contatti';

	/**
	 * Api che restituisce i contatti di un utente(telefono e cellulare)
	 *
	 * @param string $_locale
	 *
	 * @return ContattiViewModel
	 */
	public function getContatti(string $_locale) : ContattiViewModel;

	/**
	 * Api per aggiornare i contatti
	 *
	 * @param string $telefono
	 * @param string $cellulare
	 *
	 * @return array
	 */
	public function aggiornaContatti(string $telefono, string $cellulare) : array;
}
