<?php
declare(strict_types=1);

namespace App\ViewModel;

final class AndamentoAnnualeCarrieraViewModel{
	/**
	 * @param int    $id
	 * @param string $mese
	 * @param string $qualifica
	 * @param string $style
	 * @param bool   $attivo
	 * @param array  $condizioni
	 * @param array  $punti
	 */
	public function __construct(private int $id, private string $mese, private string $qualifica, private string $style, private bool $attivo, private array $condizioni, private array $punti){
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
	public function getMese() : string{
		return $this->mese;
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
	public function getStyle() : string{
		return $this->style;
	}

	/**
	 * @return bool
	 */
	public function isAttivo() : bool{
		return $this->attivo;
	}

	/**
	 * @return array
	 */
	public function getCondizioni() : array{
		return $this->condizioni;
	}

	/**
	 * @return array
	 */
	public function getPunti() : array{
		return $this->punti;
	}
}
