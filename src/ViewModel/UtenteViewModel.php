<?php
declare(strict_types=1);

namespace App\ViewModel;

final class UtenteViewModel{
	/**
	 * @param int    $id
	 * @param string $codice
	 * @param string $nominativo
	 */
	public function __construct(private int $id, private string $codice, private string $nominativo){
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
}
