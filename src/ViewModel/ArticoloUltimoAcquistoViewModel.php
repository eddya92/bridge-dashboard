<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ArticoloUltimoAcquistoViewModel{
	/**
	 * @param int $id
	 * @param string $nome
	 * @param string $codice
	 * @param string $categoria
	 * @param string $descrizione
	 * @param string $foto
	 */
	public function __construct(private int $id, private string $nome, private string $codice, private string $categoria, private string $descrizione, private string $foto){
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
	public function getCodice() : string{
		return $this->codice;
	}

	/**
	 * @return string
	 */
	public function getCategoria() : string{
		return $this->categoria;
	}

	/**
	 * @return string
	 */
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return string
	 */
	public function getFoto() : string{
		return $this->foto;
	}
}
