<?php
declare(strict_types=1);

namespace App\ViewModel;

final class DettaglioOrdineViewModel{
	/**
	 * @param int    $id
	 * @param string $codice
	 * @param array  $azienda
	 * @param string $data
	 * @param array  $esito
	 * @param array  $sponsor
	 * @param array  $intestatario
	 * @param array  $spedizione
	 * @param array  $articoli
	 * @param array  $modalitaPagamento
	 * @param array  $modalitaSpedizione
	 * @param array  $speseAmministrative
	 * @param string $totale
	 * @param string $punti
	 * @param string $note
	 * @param array  $pagamento
	 */
	public function __construct(private int $id, private string $codice, private array $azienda, private string $data, private array $esito, private array $sponsor, private array $intestatario, private array $spedizione, private array $articoli, private array $modalitaPagamento, private array $modalitaSpedizione, private array $speseAmministrative, private string $totale, private string $punti, private string $note, private array $pagamento){
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
	 * @return array
	 */
	public function getAzienda() : array{
		return $this->azienda;
	}

	/**
	 * @return string
	 */
	public function getData() : string{
		return $this->data;
	}

	/**
	 * @return array
	 */
	public function getEsito() : array{
		return $this->esito;
	}

	/**
	 * @return array
	 */
	public function getSponsor() : array{
		return $this->sponsor;
	}

	/**
	 * @return array
	 */
	public function getIntestatario() : array{
		return $this->intestatario;
	}

	/**
	 * @return array
	 */
	public function getSpedizione() : array{
		return $this->spedizione;
	}

	/**
	 * @return array
	 */
	public function getArticoli() : array{
		return $this->articoli;
	}

	/**
	 * @return array
	 */
	public function getModalitaPagamento() : array{
		return $this->modalitaPagamento;
	}

	/**
	 * @return array
	 */
	public function getModalitaSpedizione() : array{
		return $this->modalitaSpedizione;
	}

	/**
	 * @return array
	 */
	public function getSpeseAmministrative() : array{
		return $this->speseAmministrative;
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
	 * @return string
	 */
	public function getNote() : string{
		return $this->note;
	}

	/**
	 * @return array
	 */
	public function getPagamento() : array{
		return $this->pagamento;
	}
}
