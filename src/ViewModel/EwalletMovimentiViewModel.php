<?php
declare(strict_types=1);

namespace App\ViewModel;

/**
 * @param string $data_operazione
 * @param string $operazione
 * @param string $dettaglio
 * @param string $link
 * @param string $tipo_operazione
 * @param string $colore
 * @param string $visible
 * @param string $costo_operazione
 *
 */
final class EwalletMovimentiViewModel{
	public function __construct(private string $data_operazione, private string $operazione, private string $dettaglio, private string $costo, private string $tipo_operazione, private string $colore, private string $visibile, private string $costo_operazione){
	}

	/**
	 * @return string
	 */
	public function getDataOperazione() : string{
		return $this->data_operazione;
	}

	/**
	 * @return string
	 */
	public function getOperazione() : string{
		return $this->operazione;
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
	public function getCosto() : string{
		return $this->costo;
	}

	/**
	 * @return string
	 */
	public function getTipoOperazione() : string{
		return $this->tipo_operazione;
	}

	/**
	 * @return string
	 */
	public function getColore() : string{
		return $this->colore;
	}

	/**
	 * @return string
	 */
	public function getVisibile() : string{
		return $this->visibile;
	}

	/**
	 * @return string
	 */
	public function getCostoOperazione() : string{
		return $this->costo_operazione;
	}
}
