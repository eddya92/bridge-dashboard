<?php
declare(strict_types=1);

namespace App\ViewModel;

final class TotaleViewModel{
	/**
	 * @param string $titolo
	 * @param string $valore
	 * @param string $dettaglio
	 * @param string $link
	 * @param string $button
	 */
	public function __construct( private string $titolo, private string $valore, private string $dettaglio, private string $link, private string $button){
	}

	/**
	 * @return string
	 */
	public function getTitolo() : string{
		return $this->titolo;
	}

	/**
	 * @return string
	 */
	public function getValore() : string{
		return $this->valore;
	}

	/**
	 * @return string
	 */
	public function getDettaglio() : string{
		return $this->dettaglio;
	}

	/**
	 * @return string
	 */
	public function getLink() : string{
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getButton() : string{
		return $this->button;
	}
}
