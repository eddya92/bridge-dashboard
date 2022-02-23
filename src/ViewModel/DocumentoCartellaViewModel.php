<?php
declare(strict_types=1);

namespace App\ViewModel;

final class DocumentoCartellaViewModel{
	/**
	 * @param int    $id
	 * @param string $nome
	 * @param string $descrizione
	 * @param string $breadcrumb
	 * @param array  $cartellaSuperiore
	 * @param array  $files
	 */
	public function __construct(private int $id, private string $nome, private string $descrizione, private string $breadcrumb, private array $cartellaSuperiore, private array $files){
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
	 * @return string
	 */
	public function getBreadcrumb() : string{
		return $this->breadcrumb;
	}

	/**
	 * @return array
	 */
	public function getCartellaSuperiore() : array{
		return $this->cartellaSuperiore;
	}

	/**
	 * @return array
	 */
	public function getFiles() : array{
		return $this->files;
	}
}
