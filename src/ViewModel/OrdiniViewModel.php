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
	 */
	public function __construct(private int $id, private string $data, private string $codice, private string $user, private float $pc, private string $totale, private string $esito, private string $esitoColore, private bool $visibile){
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
	public function getData() : string{
		return $this->data;
	}

	/**
	 * @return string
	 */
	public function getCodice() : string{
		return $this->codice;
	}

	/**
	 * @return string
	 */
	public function getUser() : string{
		return $this->user;
	}

	/**
	 * @return float
	 */
	public function getPc() : float{
		return $this->pc;
	}

	/**
	 * @return string
	 */
	public function getTotale() : string{
		return $this->totale;
	}

	/**
	 * @return string
	 */
	public function getEsito() : string{
		return $this->esito;
	}

	/**
	 * @return string
	 */
	public function getEsitoColore() : string{
		return $this->esitoColore;
	}

	/**
	 * @return bool
	 */
	public function isVisibile() : bool{
		return $this->visibile;
	}
}
