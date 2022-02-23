<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ModalitaSpedizioneViewModel{
	/**
	 * @param int    $id
	 * @param string $tipo
	 * @param string $spesa
	 */
	public function __construct(private int $id, private string $tipo, private string $spesa){
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
	public function getTipo() : string{
		return $this->tipo;
	}

	/**
	 * @return string
	 */
	public function getSpesa() : string{
		return $this->spesa;
	}
}
