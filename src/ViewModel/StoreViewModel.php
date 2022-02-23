<?php
declare(strict_types=1);

namespace App\ViewModel;

final class StoreViewModel{
	/**
	 * @param int    $id
	 * @param string $codice
	 * @param string $insegna
	 * @param string $listino
	 * @param string $sellingZone
	 * @param string $foto
	 * @param string $logo
	 * @param array  $tipiOrdine
	 */
	public function __construct(private int $id, private string $codice, private string $insegna, private string $listino, private string $sellingZone, private string $foto, private string $logo, private array $tipiOrdine){
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
	public function getCodice() : string{
		return $this->codice;
	}

	/**
	 * @return string
	 */
	public function getInsegna() : string{
		return $this->insegna;
	}

	/**
	 * @return string
	 */
	public function getListino() : string{
		return $this->listino;
	}

	/**
	 * @return string
	 */
	public function getSellingZone() : string{
		return $this->sellingZone;
	}

	/**
	 * @return string
	 */
	public function getFoto() : string{
		return $this->foto;
	}

	/**
	 * @return string
	 */
	public function getLogo() : string{
		return $this->logo;
	}

	/**
	 * @return array
	 */
	public function getTipiOrdine() : array{
		return $this->tipiOrdine;
	}


}
