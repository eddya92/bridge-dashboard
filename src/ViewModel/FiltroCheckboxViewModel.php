<?php
declare(strict_types=1);

namespace App\ViewModel;

/**
 * @param string $id
 * @param string $tipo
 * @param string $nome
 * @param string $valore
 */
final class FiltroCheckboxViewModel{
	public function __construct(private string $id, private string $tipo, private string $nome, private int $valore){
	}

	/**
	 * @return string
	 */
	public function getId() : string{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTipo() : string{
		return $this->tipo;
	}

	/**
	 * @return string
	 */
	public function getNome() : string{
		return $this->nome;
	}

	/**
	 * @return int
	 */
	public function getValore() : int{
		return $this->valore;
	}
}
