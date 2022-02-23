<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\AccountViewModel;
use App\ViewModel\ResidenzaViewModel;

interface AccountRepository{

	public const TAG_ACCOUNT = 'account';
	public const TAG_ACCOUNT_RESIDENZA = 'residenza';

	public function getAccount(string $codice, string $locale) : ?AccountViewModel;

	public function aggiornaDatiAccount(string $vecchiaPassword, string $nuovaPassword, string $confermaPassword) : array;

	public function richiediOblioAccount() : array;

	public function registrazioneUtente(string $codiceSponsor, string $nome, string $cognome, string $email, string $password, string $nazione, array $agreements) : array;

	public function registrazioneCliente(string $codiceSponsor, string $nome, string $cognome, string $ragioneSociale, string $naturaGiuridica, string $email, string $password, string $nazione, array $agreements) : array;

	public function getResidenza(string $locale) : ?ResidenzaViewModel;

	public function aggiornaDatiResidenza(string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione) : array;

	public function inviaMailRecuperoPassword(string $email) : array;
}
