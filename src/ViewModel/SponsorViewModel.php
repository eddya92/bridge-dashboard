<?php
declare(strict_types=1);

namespace App\ViewModel;

final class SponsorViewModel{
	/**
	 * @param string $nome
	 * @param string $cognome
	 * @param string $nominativo
	 * @param string $qualifica
	 * @param string $codice
	 * @param string $email
	 * @param string $telefono
	 */
	public function __construct(private string $nome, private string $cognome, private string $nominativo, private string $qualifica, private string $codice, private string $email, private string $telefono){
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
	public function getQualifica() : string{
		return $this->qualifica;
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
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getTelefono() : string{
		return $this->telefono;
	}
}
