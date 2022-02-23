<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ContattiViewModel{
	/**
	 * @param string $telefono
	 * @param string $cellulare
	 */
	public function __construct(private string $telefono, private string $cellulare){

	}

	/**
	 * @return string
	 */
	public function getTelefono() : string{
		return $this->telefono;
	}

	/**
	 * @return string
	 */
	public function getCellulare() : string{
		return $this->cellulare;
	}
}
