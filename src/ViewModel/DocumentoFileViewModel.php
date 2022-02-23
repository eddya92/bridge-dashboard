<?php
declare(strict_types=1);

namespace App\ViewModel;

final class DocumentoFileViewModel{
	/**
	 * @param int    $id
	 * @param string $nome
	 * @param string $descrizione
	 * @param bool   $cartella
	 * @param int $numeroDocumenti
	 * @param string $estensione
	 * @param int $dimensione
	 * @param string $link
	 */
	public function __construct(private int $id, private string $nome, private string $descrizione, private bool $cartella, private int $numeroDocumenti, private string $estensione, private int $dimensione, private string $link){
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
	public function getNome() : string{
		return $this->nome;
	}

	/**
	 * @return string
	 */
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return bool
	 */
	public function isCartella() : bool{
		return $this->cartella;
	}

	/**
	 * @return int
	 */
	public function getNumeroDocumenti() : int{
		return $this->numeroDocumenti;
	}

	/**
	 * @return string
	 */
	public function getEstensione() : string{
		return $this->estensione;
	}

	/**
	 * @return int
	 */
	public function getDimensione() : int{
		return $this->dimensione;
	}

	/**
	 * @return string
	 */
	public function getLink() : string{
		return $this->link;
	}
}
