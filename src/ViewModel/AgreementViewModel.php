<?php
declare(strict_types=1);

namespace App\ViewModel;

final class AgreementViewModel{
	/**
	 * @param int    $id
	 * @param string $nome
	 * @param string $descrizione
	 * @param string $link
	 * @param bool   $required
	 */
	public function __construct(private int $id, private string $nome, private string $descrizione, private string $link, private bool $required){
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
	public function getLink() : string{
		return $this->link;
	}

	/**
	 * @return bool
	 */
	public function isRequired() : bool{
		return $this->required;
	}
}
