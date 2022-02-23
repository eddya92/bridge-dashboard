<?php
declare(strict_types=1);

namespace App\ViewModel;

final class NazioneViewModel{
	/**
	 * @param string $id
	 * @param string $nome
	 * @param string $icona
	 */
	public function __construct(private string $id, private string $nome, private string $icona){
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
	public function getNome() : string{
		return $this->nome;
	}

	/**
	 * @return string
	 */
	public function getIcona() : string{
		return $this->icona;
	}
}
