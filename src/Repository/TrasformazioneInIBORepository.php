<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface TrasformazioneInIBORepository{
	public function trasformaInIbo(
		string $codice,
		string $codice_fiscale,
		string $piva,
		string $pec,
		string $codice_univoco,
		string $indirizzo,
		string $numeroCivico,
		string $cap,
		string $comune,
		string $provincia,
		string $telefono,
		string $cellulare) : array;
}
