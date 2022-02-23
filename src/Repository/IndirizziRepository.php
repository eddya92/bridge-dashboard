<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\IndirizzoSpedizioneViewModel;
use Generator;

interface IndirizziRepository{
	public const TAG_INDIRIZZI = 'indirizzi';

	public function getElencoIndirizziSpedizioneSalvati(string $_locale) : ?Generator;

	public function getIndirizzoSpedizione(int $id,  string $_locale) : ?IndirizzoSpedizioneViewModel;

	public function eliminaIndirizzoSpedizione(int $id) : array;

	public function aggiornaDatiSpedizione(
		int    $id,
		string $nome,
		string $cognome,
		string $indirizzo,
		string $numeroCivico,
		string $cap,
		string $comune,
		string $provincia,
		string $nazione,
		string $email,
		string $numeroTelefono,
		string $note,
		bool   $isPrincipale,
		bool   $consegnaSabato,
	) : array;
}
