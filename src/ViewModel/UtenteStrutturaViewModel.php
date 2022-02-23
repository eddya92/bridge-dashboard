<?php
declare(strict_types=1);

namespace App\ViewModel;

final class UtenteStrutturaViewModel{
	/**
	 * @param int    $id
	 * @param string $codice
	 * @param string $nominativo
	 * @param string $email
	 * @param string $cellulare
	 * @param int $livello
	 * @param string $qualifica
	 * @param string $colore
	 * @param string $sponsor
	 */
	public function __construct(private int $id, private string $codice, private string $nominativo, private string $email, private string $cellulare, private int $livello, private string $qualifica, private string $colore, private string $sponsor){
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
	public function getCodice() : string{
		return $this->codice;
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
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getCellulare() : string{
		return $this->cellulare;
	}

	/**
	 * @return int
	 */
	public function getLivello() : int{
		return $this->livello;
	}

	/**
	 * @return string
	 */
	public function getQualifica() : string{
		return $this->qualifica;
	}

	/**
	 * @return string
	 */
	public function getColore() : string{
		return $this->colore;
	}

	/**
	 * @return string
	 */
	public function getSponsor() : string{
		return $this->sponsor;
	}
}
