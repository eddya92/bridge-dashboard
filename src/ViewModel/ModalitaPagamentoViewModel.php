<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ModalitaPagamentoViewModel{
	public function __construct(private int $id, private string $tipo, private string $spesa, private string $foto){
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

	/**
	 * @return string
	 */
	public function getFoto() : string{
		return $this->foto;
	}
}
