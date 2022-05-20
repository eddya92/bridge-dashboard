<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\MessaggioViewModel;
use Generator;

interface MessaggiRepository{
	public const TAG_MESSAGGGI = 'messaggi';

	/**
	 * Ritorna l'ultimo messaggio disponibile
	 *
	 * @param string $locale
	 *
	 * @return MessaggioViewModel|null
	 */
	public function getUltimoMessaggio(string $locale) : ?MessaggioViewModel;

	/**
	 * @param string $_locale
	 *
	 * @return \Generator
	 */
	public function getMessaggi(string $_locale) : Generator;

	/**
	 * @param int    $id
	 * @param string $_locale
	 *
	 * @return MessaggioViewModel
	 */
	public function getMessaggio(int $id, string $_locale) : MessaggioViewModel;
}
