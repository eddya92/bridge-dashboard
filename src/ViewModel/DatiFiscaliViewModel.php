<?php
declare(strict_types=1);

namespace App\ViewModel;

final class DatiFiscaliViewModel{
	/**
	 * @param string $codiceFiscale
	 * @param string $piva
	 * @param string $pec
	 * @param string $codiceUnivoco
	 */
	public function __construct(private string $codiceFiscale, private string $piva, private string $pec, private string $codiceUnivoco){
	}

	/**
	 * @return string
	 */
	public function getCodiceFiscale() : string{
		return $this->codiceFiscale;
	}

	/**
	 * @return string
	 */
	public function getPiva() : string{
		return $this->piva;
	}

	/**
	 * @return string
	 */
	public function getPec() : string{
		return $this->pec;
	}

	/**
	 * @return string
	 */
	public function getCodiceUnivoco() : string{
		return $this->codiceUnivoco;
	}

}
