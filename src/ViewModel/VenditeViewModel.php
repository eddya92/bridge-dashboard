<?php
declare(strict_types=1);

namespace App\ViewModel;

final class VenditeViewModel{
	/**
	 * @param array $totali
	 * @param array $andamento
	 */
	public function __construct(private array $totali, private array $andamento){
	}

	/**
	 * @return array
	 */
	public function getTotali() : array{
		return $this->totali;
	}

	/**
	 * @return array
	 */
	public function getAndamento() : array{
		return $this->andamento;
	}
}
