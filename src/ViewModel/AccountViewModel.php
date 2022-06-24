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
	 * @param string $superiore
	 * @param string $locale
	 */
	public function __construct(private int $id, private string $foto, private string $nazioneResidenza, private string $ruolo, private string $nome, private string $cognome, private string $nominativo, private string $qualifica, private string $codice, private string $dataIscrizione, private string $codiceFiscale, private string $telefono, private string $email, private array $carriera, private array $oblio, private string $cellulare, private string $superiore, private string $locale){
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
	public function getFoto() : string{
		return $this->foto;
	}

	/**
	 * @param string $foto
	 */
	public function setFoto(string $foto) : void{
		$this->foto = $foto;
	}

	/**
	 * @return string
	 */
	public function getNazioneResidenza() : string{
		return $this->nazioneResidenza;
	}

	/**
	 * @param string $nazioneResidenza
	 */
	public function setNazioneResidenza(string $nazioneResidenza) : void{
		$this->nazioneResidenza = $nazioneResidenza;
	}

	/**
	 * @return string
	 */
	public function getRuolo() : string{
		return $this->ruolo;
	}

	/**
	 * @param string $ruolo
	 */
	public function setRuolo(string $ruolo) : void{
		$this->ruolo = $ruolo;
	}

	/**
	 * @return string
	 */
	public function getNome() : string{
		return $this->nome;
	}

	/**
	 * @param string $nome
	 */
	public function setNome(string $nome) : void{
		$this->nome = $nome;
	}

	/**
	 * @return string
	 */
	public function getCognome() : string{
		return $this->cognome;
	}

	/**
	 * @param string $cognome
	 */
	public function setCognome(string $cognome) : void{
		$this->cognome = $cognome;
	}

	/**
	 * @return string
	 */
	public function getNominativo() : string{
		return $this->nominativo;
	}

	/**
	 * @param string $nominativo
	 */
	public function setNominativo(string $nominativo) : void{
		$this->nominativo = $nominativo;
	}

	/**
	 * @return string
	 */
	public function getQualifica() : string{
		return $this->qualifica;
	}

	/**
	 * @param string $qualifica
	 */
	public function setQualifica(string $qualifica) : void{
		$this->qualifica = $qualifica;
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
	public function getDataIscrizione() : string{
		return $this->dataIscrizione;
	}

	/**
	 * @param string $dataIscrizione
	 */
	public function setDataIscrizione(string $dataIscrizione) : void{
		$this->dataIscrizione = $dataIscrizione;
	}

	/**
	 * @return string
	 */
	public function getCodiceFiscale() : string{
		return $this->codiceFiscale;
	}

	/**
	 * @param string $codiceFiscale
	 */
	public function setCodiceFiscale(string $codiceFiscale) : void{
		$this->codiceFiscale = $codiceFiscale;
	}

	/**
	 * @return string
	 */
	public function getTelefono() : string{
		return $this->telefono;
	}

	/**
	 * @param string $telefono
	 */
	public function setTelefono(string $telefono) : void{
		$this->telefono = $telefono;
	}

	/**
	 * @return string
	 */
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email) : void{
		$this->email = $email;
	}

	/**
	 * @return array
	 */
	public function getCarriera() : array{
		return $this->carriera;
	}

	/**
	 * @param array $carriera
	 */
	public function setCarriera(array $carriera) : void{
		$this->carriera = $carriera;
	}

	/**
	 * @return array
	 */
	public function getOblio() : array{
		return $this->oblio;
	}

	/**
	 * @param array $oblio
	 */
	public function setOblio(array $oblio) : void{
		$this->oblio = $oblio;
	}

	/**
	 * @return string
	 */
	public function getCellulare() : string{
		return $this->cellulare;
	}

	/**
	 * @param string $cellulare
	 */
	public function setCellulare(string $cellulare) : void{
		$this->cellulare = $cellulare;
	}

	/**
	 * @return string
	 */
	public function getSuperiore() : string{
		return $this->superiore;
	}

	/**
	 * @param string $superiore
	 */
	public function setSuperiore(string $superiore) : void{
		$this->superiore = $superiore;
	}

	/**
	 * @return string
	 */
	public function getLocale() : string{
		return $this->locale;
	}
}
