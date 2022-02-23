<?php
declare(strict_types=1);

namespace App\ViewModel;

final class AccountViewModel{
	/**
	 * @param int    $id
	 * @param string $foto
	 * @param string $nazioneResidenza
	 * @param string $ruolo
	 * @param string $nome
	 * @param string $cognome
	 * @param string $nominativo
	 * @param string $qualifica
	 * @param string $codice
	 * @param string $dataIscrizione
	 * @param string $codiceFiscale
	 * @param string $telefono
	 * @param string $email
	 * @param array  $carriera
	 * @param array  $oblio
	 * @param string $cellulare
	 */
	public function __construct(private int $id, private string $foto, private string $nazioneResidenza, private string $ruolo, private string $nome, private string $cognome, private string $nominativo, private string $qualifica, private string $codice, private string $dataIscrizione, private string $codiceFiscale, private string $telefono, private string $email, private array $carriera, private array $oblio, private string $cellulare){
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
	public function getFoto() : string{
		return $this->foto;
	}

	/**
	 * @return string
	 */
	public function getNazioneResidenza() : string{
		return $this->nazioneResidenza;
	}

	/**
	 * @return string
	 */
	public function getRuolo() : string{
		return $this->ruolo;
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
	public function getDataIscrizione() : string{
		return $this->dataIscrizione;
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
	public function getTelefono() : string{
		return $this->telefono;
	}

	/**
	 * @return string
	 */
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @return array
	 */
	public function getCarriera() : array{
		return $this->carriera;
	}

	/**
	 * @return array
	 */
	public function getOblio() : array{
		return $this->oblio;
	}

	/**
	 * @return string
	 */
	public function getCellulare() : string{
		return $this->cellulare;
	}


}
