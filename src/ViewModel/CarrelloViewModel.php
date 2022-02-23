<?php
declare(strict_types=1);

namespace App\ViewModel;

final class CarrelloViewModel{
	public function __construct(private string $id, private string $totale, private string $punti, private array $articoli, private string $valuta, private int $quantita_totale){
	}

	/**
	 * @return int
	 */
	public function getQuantitaTotale() : int{
		return $this->quantita_totale;
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
	public function getId() : string{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTotale() : string{
		return $this->totale;
	}

	/**
	 * @return string
	 */
	public function getPunti() : string{
		return $this->punti;
	}

	/**
	 * @return array
	 */
	public function getArticoli() : array{
		return $this->articoli;
	}
}
