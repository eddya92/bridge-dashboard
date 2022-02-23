<?php
declare(strict_types=1);

namespace App\ViewModel;


final class IndirizzoSpedizioneViewModel{
	/**
	 * @param int    $id
	 * @param string $nominativo
	 * @param string $nazione
	 * @param string $indirizzo
	 * @param string $civico
	 * @param string $comune
	 * @param string $provincia
	 * @param string $cap
	 * @param string $email
	 * @param string $numeroTelefono
	 * @param string $nota
	 * @param bool   $consegnaSabato
	 * @param bool   $isPrincipale
	 * @param bool   $isRaggiungibileDalCorriere
	 * @param string $nome
	 * @param string $cognome
	 * @param string $ragioneSociale
	 */
	public function __construct(private int $id, private string $nominativo, private string $nazione, private string $indirizzo, private string $civico, private string $comune, private string $provincia, private string $cap, private string $email, private string $numeroTelefono, private string $nota, private bool $consegnaSabato, private bool $isPrincipale, private bool $isRaggiungibileDalCorriere = false, private string $nome = '', private string $cognome = '', private string $ragioneSociale = ''){
	}

	/**
	 * @return string
	 */
	public function getNome() : string{
		return $this->nome;
	}

	/**
	 * @return string
	 */
	public function getCognome() : string{
		return $this->cognome;
	}

	/**
	 * @return string
	 */
	public function getRagioneSociale() : string{
		return $this->ragioneSociale;
	}

	/**
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getNominativo() : string{
		return $this->nominativo;
	}

	/**
	 * @return string
	 */
	public function getNazione() : string{
		return $this->nazione;
	}

	/**
	 * @return string
	 */
	public function getIndirizzo() : string{
		return $this->indirizzo;
	}

	/**
	 * @return string
	 */
	public function getCivico() : string{
		return $this->civico;
	}

	/**
	 * @return string
	 */
	public function getComune() : string{
		return $this->comune;
	}

	/**
	 * @return string
	 */
	public function getProvincia() : string{
		return $this->provincia;
	}

	/**
	 * @return string
	 */
	public function getCap() : string{
		return $this->cap;
	}

	/**
	 * @return string
	 */
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getNumeroTelefono() : string{
		return $this->numeroTelefono;
	}

	/**
	 * @return string
	 */
	public function getNota() : string{
		return $this->nota;
	}

	/**
	 * @return bool
	 */
	public function isConsegnaSabato() : bool{
		return $this->consegnaSabato;
	}

	/**
	 * @return bool
	 */
	public function isPrincipale() : bool{
		return $this->isPrincipale;
	}

	/**
	 * @return bool
	 */
	public function isRaggiungibileDalCorriere() : bool{
		return $this->isRaggiungibileDalCorriere;
	}
}
