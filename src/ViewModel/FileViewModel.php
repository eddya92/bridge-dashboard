<?php
declare(strict_types=1);

namespace App\ViewModel;


final class FileViewModel{
	/**
	 * @param string $nome
	 * @param int    $id
	 * @param bool   $cartella
	 * @param array  $cartelle
	 */
	public function __construct(private string $nome, private int $id, private bool $cartella, private array $cartelle){
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
	public function getId() : int{
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function isCartella() : bool{
		return $this->cartella;
	}

	/**
	 * @return array
	 */
	public function getCartelle() : array{
		return $this->cartelle;
	}


}
