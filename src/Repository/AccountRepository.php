<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\AccountViewModel;
use App\ViewModel\ResidenzaViewModel;

interface AccountRepository{
	public const TAG_ACCOUNT = 'account';
	public const TAG_ACCOUNT_RESIDENZA = 'residenza';

	/**
	 * Restituisce un entità AccountViewModel in base al codice utente che viene passato, nella lingua che viene passata
	 *
	 * @param string $codice
	 * @param string $locale
	 *
	 * @return AccountViewModel|null
	 */
	public function getAccount(string $codice, string $locale) : AccountViewModel;

	/**
	 * Metodo che permette di modificare la password di un utente
	 *
	 * @param string $vecchiaPassword
	 * @param string $nuovaPassword
	 * @param string $confermaPassword
	 *
	 * @return array
	 */
	public function aggiornaDatiAccount(string $vecchiaPassword, string $nuovaPassword, string $confermaPassword) : array;

	/**
	 * Metodo per richiedere oblio dell'utente loggato
	 */
	public function richiediOblioAccount() : array;

	/**
	 * Registrazione incaricato
	 *
	 * @param string $codiceSponsor
	 * @param string $nome
	 * @param string $cognome
	 * @param string $email
	 * @param string $password
	 * @param string $nazione
	 * @param array  $agreements
	 *
	 * @return array
	 */
	public function registrazioneUtente(string $codiceSponsor, string $nome, string $cognome, string $email, string $password, string $nazione, array $agreements) : array;

	/**
	 * Registrazione Cliente
	 *
	 * @param string $codiceSponsor
	 * @param string $nome
	 * @param string $cognome
	 * @param string $ragioneSociale
	 * @param string $naturaGiuridica
	 * @param string $email
	 * @param string $password
	 * @param string $nazione
	 * @param array  $agreements
	 *
	 * @return array
	 */
	public function registrazioneCliente(string $codiceSponsor, string $nome, string $cognome, string $ragioneSociale, string $naturaGiuridica, string $email, string $password, string $nazione, array $agreements) : array;

	/**
	 * Metodo per avere la residenza dell'utente loggato
	 *
	 * Ritorna un oggetto di tipo Residenza view model(oppure lancia un eccezione)
	 *
	 * @param string $locale
	 *
	 * @return \App\ViewModel\ResidenzaViewModel
	 */
	public function getResidenza(string $locale) : ResidenzaViewModel;

	/**
	 * Aggiorna i dati di residenza
	 *
	 * @param string $nome
	 * @param string $cognome
	 * @param string $indirizzo
	 * @param string $numeroCivico
	 * @param string $cap
	 * @param string $comune
	 * @param string $provincia
	 * @param string $nazione
	 *
	 */
	public function aggiornaDatiResidenza(string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione);

	/**
	 * Invia email per il recupero password
	 *
	 */
	public function inviaMailRecuperoPassword(string $email);
}
