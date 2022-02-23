<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ResidenzaViewModel{


	/**
	 * @param string $naturaGiuridica
	 * @param string $nome
	 * @param string $cognome
	 * @param string $nominativo
	 * @param string $codiceFiscale
	 * @param string $ragioneSociale
	 * @param string $legaleRappresentante
	 * @param string $piva
	 * @param string $pec
	 * @param string $codiceUnivoco
	 * @param string $provincia
	 * @param string $cap
	 * @param string $comune
	 * @param string $indirizzo
	 * @param string $numeroCivico
	 * @param string $indirizzoCompleto
	 * @param string $countryCode
	 */
	public function __construct(private string $naturaGiuridica, private string $nome, private string $cognome, private string $nominativo, private string $codiceFiscale, private string $ragioneSociale, private string $legaleRappresentante, private string $provincia, private string $cap, private string $comune, private string $indirizzo, private string $numeroCivico, private string $indirizzoCompleto, private string $countryCode){
	}

	/**
	 * @return string
	 */
	public function getCountryCode() : string{
		return $this->countryCode;
	}

	/**
	 * @return string
	 */
	public function getNaturaGiuridica() : string{
		return $this->naturaGiuridica;
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
	public function getNominativo() : string{
		return $this->nominativo;
	}

	/**
	 * @return string
	 */
	public function getCodiceFiscale() : string{
		return $this->codiceFiscale;
	}

	/**
	 * @return string
	 */
	public function getRagioneSociale() : string{
		return $this->ragioneSociale;
	}

	/**
	 * @return string
	 */
	public function getLegaleRappresentante() : string{
		return $this->legaleRappresentante;
	}

	/**
	 * @return string
	 */
	public function getPiva() : string{
		return $this->piva;
	}

	/**
	 * @return string
	 */
	public function getPec() : string{
		return $this->pec;
	}

	/**
	 * @return string
	 */
	public function getCodiceUnivoco() : string{
		return $this->codiceUnivoco;
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
	public function getComune() : string{
		return $this->comune;
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
	public function getNumeroCivico() : string{
		return $this->numeroCivico;
	}

	/**
	 * @return string
	 */
	public function getIndirizzoCompleto() : string{
		return $this->indirizzoCompleto;
	}
}
