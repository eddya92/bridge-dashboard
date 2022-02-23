<?php
declare(strict_types=1);

namespace App\ViewModel;

final class FiltriTipoOrdineViewModel{
	/**
	 * @param int    $id
	 * @param string $filtro
	 */
	public function __construct(private int $id, private string $filtro){
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
	public function getFiltro() : string{
		return $this->filtro;
	}
}
