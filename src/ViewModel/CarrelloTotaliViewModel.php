<?php
declare(strict_types=1);

namespace App\ViewModel;

final class CarrelloTotaliViewModel{
	public function __construct(private string $carrello, private string $spese_amministrative, private string $spese_spedizione, private string $spese_pagamento, private string $totale){
	}

	/**
	 * @return string
	 */
	public function getCarrello() : string{
		return $this->carrello;
	}

	/**
	 * @return string
	 */
	public function getSpeseAmministrative() : string{
		return $this->spese_amministrative;
	}

	/**
	 * @return string
	 */
	public function getSpeseSpedizione() : string{
		return $this->spese_spedizione;
	}

	/**
	 * @return string
	 */
	public function getSpesePagamento() : string{
		return $this->spese_pagamento;
	}

	/**
	 * @return string
	 */
	public function getTotale() : string{
		return $this->totale;
	}
}
