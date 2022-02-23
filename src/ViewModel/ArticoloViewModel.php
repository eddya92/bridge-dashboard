<?php
declare(strict_types=1);

namespace App\ViewModel;

final class ArticoloViewModel{
	public function __construct(
		private int    $id,
		private string $nome,
		private string $categoria,
		private string $prezzo,
		private float  $sconto,
		private string $prezzoScontato,
		private string $codice,
		private string $punti,
		private string $tipo,
		private string $descrizione,
		private string $abstract,
		private array  $foto,
		private array  $varianti,
		private int    $quantita_multipla,
		private int    $quantita_max,
		private int    $quantita_stock
	){
	}

	/**
	 * @return string
	 */
	public function getAbstract() : string{
		return $this->abstract;
	}

	/**
	 * @return int
	 */
	public function getQuantitaStock() : int{
		return $this->quantita_stock;
	}

	/**
	 * @return int
	 */
	public function getQuantitaMax() : int{
		return $this->quantita_max;
	}

	/**
	 * @return int
	 */
	public function getQuantitaMultipla() : int{
		return $this->quantita_multipla;
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
	 * @return string
	 */
	public function getCategoria() : string{
		return $this->categoria;
	}

	/**
	 * @return string
	 */
	public function getPrezzo() : string{
		return $this->prezzo;
	}

	/**
	 * @return float
	 */
	public function getSconto() : float{
		return $this->sconto;
	}

	/**
	 * @return string
	 */
	public function getPrezzoScontato() : string{
		return $this->prezzoScontato;
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
	public function getPunti() : string{
		return $this->punti;
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
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return array
	 */
	public function getFoto() : array{
		return $this->foto;
	}

	/**
	 * @return array
	 */
	public function getVarianti() : array{
		return $this->varianti;
	}
}
