<?php
declare(strict_types=1);

namespace App\ViewModel;

final class BonusViewModel{
	/**
	 * @param string $mese
	 * @param string $meseTesto
	 * @param string $meseTestoEsteso
	 * @param string $qualifica
	 * @param string $colore
	 * @param int    $livello
	 * @param bool   $qualificato
	 * @param array  $bonus
	 * @param string $totale
	 * @param float  $importo
	 */
	public function __construct(private string $mese, private string $meseTesto, private string $meseTestoEsteso, private string $qualifica, private string $colore, private int $livello, private bool $qualificato, private array $bonus, private string $totale, private float $importo){
	}

	/**
	 * @return string
	 */
	public function getMese() : string{
		return $this->mese;
	}

	/**
	 * @return string
	 */
	public function getMeseTesto() : string{
		return $this->meseTesto;
	}

	/**
	 * @return string
	 */
	public function getMeseTestoEsteso() : string{
		return $this->meseTestoEsteso;
	}

	/**
	 * @return string
	 */
	public function getQualifica() : string{
		return $this->qualifica;
	}

	/**
	 * @return string
	 */
	public function getColore() : string{
		return $this->colore;
	}

	/**
	 * @return int
	 */
	public function getLivello() : int{
		return $this->livello;
	}

	/**
	 * @return bool
	 */
	public function isQualificato() : bool{
		return $this->qualificato;
	}

	/**
	 * @return array
	 */
	public function getBonus() : array{
		return $this->bonus;
	}

	/**
	 * @return string
	 */
	public function getTotale() : string{
		return $this->totale;
	}

	/**
	 * @return float
	 */
	public function getImporto() : float{
		return $this->importo;
	}
}
