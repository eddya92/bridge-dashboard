<?php
declare(strict_types=1);

namespace App\ViewModel;

final class QualificaViewModel{
	/**
	 * @param int    $id
	 * @param string $nome
	 * @param string $colore
	 * @param bool   $qualificaAttuale
	 * @param string $descrizione
	 * @param array  $regole
	 */
	public function __construct(private int $id, private string $nome, private string $colore, private bool $qualificaAttuale, private string $descrizione, private array $regole){
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
	public function getColore() : string{
		return $this->colore;
	}

	/**
	 * @return bool
	 */
	public function isQualificaAttuale() : bool{
		return $this->qualificaAttuale;
	}

	/**
	 * @return string
	 */
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return array
	 */
	public function getRegole() : array{
		return $this->regole;
	}

}
