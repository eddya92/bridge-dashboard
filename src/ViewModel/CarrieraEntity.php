<?php
declare(strict_types=1);

namespace App\ViewModel;

/**
 * @param string $qualifica
 * @param string $qualificaSuccessiva
 * @param int    $pcPersonali
 * @param int    $pcMancanti
 * @param int    $incaricatiNecessariProssimaQualifica
 * @param int    $incaricatiPersonali
 * @param int    $incaricatiMancanti
 */
final class CarrieraEntity{
	public function __construct(private string $qualifica, private string $qualificaSuccessiva, private int $pcQualificaSuccessiva, private int $pcPersonali, private int $pcMancanti, private int $incaricatiNecessariProssimaQualifica, private int $incaricatiPersonali, private int $incaricatiMancanti){
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
	public function getQualificaSuccessiva() : string{
		return $this->qualificaSuccessiva;
	}

	/**
	 * @return int
	 */
	public function getPcQualificaSuccessiva() : int{
		return $this->pcQualificaSuccessiva;
	}

	/**
	 * @return int
	 */
	public function getPcPersonali() : int{
		return $this->pcPersonali;
	}

	/**
	 * @return int
	 */
	public function getPcMancanti() : int{
		return $this->pcMancanti;
	}

	/**
	 * @return int
	 */
	public function getIncaricatiNecessariProssimaQualifica() : int{
		return $this->incaricatiNecessariProssimaQualifica;
	}

	/**
	 * @return int
	 */
	public function getIncaricatiPersonali() : int{
		return $this->incaricatiPersonali;
	}

	/**
	 * @return int
	 */
	public function getIncaricatiMancanti() : int{
		return $this->incaricatiMancanti;
	}
}
