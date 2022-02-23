<?php
declare(strict_types=1);

namespace App\ViewModel;

final class KitViewModel{
	/**
	 * @param int    $id
	 * @param string $nome
	 * @param float  $costo
	 * @param string $valuta
	 * @param string $formato_valuta
	 * @param array  $benefits
	 * @param string $colore
	 */
	public function __construct(private int $id, private string $nome, private float $costo, private string $valuta, private string $formato_valuta, private array $benefits, private string $colore){
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
	 * @return float
	 */
	public function getCosto() : float{
		return $this->costo;
	}

	/**
	 * @return string
	 */
	public function getValuta() : string{
		return $this->valuta;
	}

	/**
	 * @return string
	 */
	public function getFormatoValuta() : string{
		return $this->formato_valuta;
	}

	/**
	 * @return array
	 */
	public function getBenefits() : array{
		return $this->benefits;
	}

	/**
	 * @return string
	 */
	public function getColore() : string{
		return $this->colore;
	}
}
