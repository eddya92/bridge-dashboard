<?php
declare(strict_types=1);

namespace App\ViewModel;

final class LingueViewModel{
	public function __construct(private int $ID_lingua, private string $Sigla, private bool $isAttiva){
	}

	/**
	 * @return int
	 */
	public function getIDLingua() : int{
		return $this->ID_lingua;
	}

	/**
	 * @return string
	 */
	public function getSigla() : string{
		return $this->Sigla;
	}

	/**
	 * @return bool
	 */
	public function isAttiva() : bool{
		return $this->isAttiva;
	}

}
