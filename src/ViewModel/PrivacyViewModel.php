<?php
declare(strict_types=1);

namespace App\ViewModel;

final class PrivacyViewModel{
	/**
	 * @param int    $id
	 * @param string $tipo
	 * @param string $nome
	 * @param string $descrizione
	 * @param string $link
	 * @param array  $datiInPossesso
	 * @param bool   $consenso
	 *
	 */
	public function __construct(private int $id, private string $tipo, private string $nome, private string $descrizione, private string $link, private array $datiInPossesso, private bool $consenso){
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
	public function getTipo() : string{
		return $this->tipo;
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
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return string
	 */
	public function getLink() : string{
		return $this->link;
	}

	/**
	 * @return array
	 */
	public function getDatiInPossesso() : array{
		return $this->datiInPossesso;
	}

	/**
	 * @return bool
	 */
	public function isConsenso() : bool{
		return $this->consenso;
	}
}
