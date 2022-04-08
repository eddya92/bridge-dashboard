<?php
declare(strict_types=1);

namespace App\ViewModel;

final class OrdiniViewModel{
	/**
	 * @param int    $id
	 * @param string $data
	 * @param string $codice
	 * @param string $user
	 * @param float  $pc
	 * @param string $totale
	 * @param string $esito
	 * @param string $esitoColore
	 * @param bool   $visibile
	 * @param string $tipologiaOrdine
	 */
	public function __construct(private int $id, private string $data, private string $codice, private string $user, private float $pc, private string $totale, private string $esito, private string $esitoColore, private bool $visibile, private string $tipologiaOrdine){
	}

	/**
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id) : void{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getData() : string{
		return $this->data;
	}

	/**
	 * @param string $data
	 */
	public function setData(string $data) : void{
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function getCodice() : string{
		return $this->codice;
	}

	/**
	 * @param string $codice
	 */
	public function setCodice(string $codice) : void{
		$this->codice = $codice;
	}

	/**
	 * @return string
	 */
	public function getUser() : string{
		return $this->user;
	}

	/**
	 * @param string $user
	 */
	public function setUser(string $user) : void{
		$this->user = $user;
	}

	/**
	 * @return float
	 */
	public function getPc() : float{
		return $this->pc;
	}

	/**
	 * @param float $pc
	 */
	public function setPc(float $pc) : void{
		$this->pc = $pc;
	}

	/**
	 * @return string
	 */
	public function getTotale() : string{
		return $this->totale;
	}

	/**
	 * @param string $totale
	 */
	public function setTotale(string $totale) : void{
		$this->totale = $totale;
	}

	/**
	 * @return string
	 */
	public function getEsito() : string{
		return $this->esito;
	}

	/**
	 * @param string $esito
	 */
	public function setEsito(string $esito) : void{
		$this->esito = $esito;
	}

	/**
	 * @return string
	 */
	public function getEsitoColore() : string{
		return $this->esitoColore;
	}

	/**
	 * @param string $esitoColore
	 */
	public function setEsitoColore(string $esitoColore) : void{
		$this->esitoColore = $esitoColore;
	}

	/**
	 * @return bool
	 */
	public function isVisibile() : bool{
		return $this->visibile;
	}

	/**
	 * @param bool $visibile
	 */
	public function setVisibile(bool $visibile) : void{
		$this->visibile = $visibile;
	}

	/**
	 * @return string
	 */
	public function getTipologiaOrdine() : string{
		return $this->tipologiaOrdine;
	}

	/**
	 * @param string $tipologiaOrdine
	 */
	public function setTipologiaOrdine(string $tipologiaOrdine) : void{
		$this->tipologiaOrdine = $tipologiaOrdine;
	}


}
