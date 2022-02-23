<?php
declare(strict_types=1);

namespace App\ViewModel;

/**
 * @param string    $id
 * @param string $tipo
 * @param string $nome
 * @param string $prefix
 * @param float  $min
 * @param float  $max
 */
final class FiltroRangeViewModel{
	public function __construct(private string $id, private string $tipo, private string $nome, private string $prefix, private float $min, private float $max){
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
	 * @return string
	 */
	public function getPrefix() : string{
		return $this->prefix;
	}

	/**
	 * @return float
	 */
	public function getMin() : float{
		return $this->min;
	}

	/**
	 * @return float
	 */
	public function getMax() : float{
		return $this->max;
	}
}
