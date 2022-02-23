<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ClientiViewModel{
	/**
	 * @param string $dataIscrizione
	 * @param string $codice
	 * @param string $nominativo
	 * @param string $email
	 * @param string $telefono
	 * @param int    $pc
	 * @param int    $ordini
	 */
	public function __construct(private string $dataIscrizione, private string $codice, private string $nominativo, private string $email, private string $telefono, private int $pc, private int $ordini){
	}

	/**
	 * @return string
	 */
	public function getDataIscrizione() : string{
		return $this->dataIscrizione;
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
	public function getTelefono() : string{
		return $this->telefono;
	}

	/**
	 * @return int
	 */
	public function getPc() : int{
		return $this->pc;
	}

	/**
	 * @return int
	 */
	public function getOrdini() : int{
		return $this->ordini;
	}
}
