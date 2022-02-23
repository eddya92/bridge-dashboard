<?php
declare(strict_types=1);

namespace App\ViewModel;

final class DocumentoPersonaleViewModel{
	/**
	 * @param string $documento
	 * @param bool   $caricato
	 * @param string $dataCaricamento
	 * @param string $link
	 * @param bool   $obbligatorio
	 * @param bool   $tesserino
	 * @param bool   $caricabile
	 * @param string $descrizione
	 * @param int    $id
	 */
	public function __construct(private string $documento, private bool $caricato, private string $dataCaricamento, private string $link, private bool $obbligatorio, private bool $tesserino, private bool $caricabile, private string $descrizione, private int $id){

	}

	/**
	 * @return string
	 */
	public function getDocumento() : string{
		return $this->documento;
	}

	/**
	 * @return bool
	 */
	public function isCaricato() : bool{
		return $this->caricato;
	}

	/**
	 * @return string
	 */
	public function getDataCaricamento() : string{
		return $this->dataCaricamento;
	}

	/**
	 * @return string
	 */
	public function getLink() : string{
		return $this->link;
	}

	/**
	 * @return bool
	 */
	public function isObbligatorio() : bool{
		return $this->obbligatorio;
	}

	/**
	 * @return bool
	 */
	public function isTesserino() : bool{
		return $this->tesserino;
	}

	/**
	 * @return bool
	 */
	public function isCaricabile() : bool{
		return $this->caricabile;
	}

	/**
	 * @return string
	 */
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}
}
