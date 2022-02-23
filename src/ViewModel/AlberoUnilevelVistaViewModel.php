<?php
declare(strict_types=1);

namespace App\ViewModel;

final class AlberoUnilevelVistaViewModel{
	/**
	 * @param int    $id
	 * @param string $nome
	 * @param bool   $principale
	 * @param int    $idUtente
	 * @param string $nomeUtente
	 * @param int    $livelli
	 * @param string $mese
	 * @param string $altezza
	 * @param string $larghezza
	 * @param string $orientamento
	 * @param string $punti
	 */
	public function __construct(private int $id, private string $nome, private bool $principale, private int $idUtente, private string $nomeUtente, private int $livelli, private string $mese, private string $altezza, private string $larghezza, private string $orientamento, private string $punti){
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
	 * @return bool
	 */
	public function isPrincipale() : bool{
		return $this->principale;
	}

	/**
	 * @return int
	 */
	public function getIdUtente() : int{
		return $this->idUtente;
	}

	/**
	 * @return string
	 */
	public function getNomeUtente() : string{
		return $this->nomeUtente;
	}

	/**
	 * @return int
	 */
	public function getLivelli() : int{
		return $this->livelli;
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
	public function getAltezza() : string{
		return $this->altezza;
	}

	/**
	 * @return string
	 */
	public function getLarghezza() : string{
		return $this->larghezza;
	}

	/**
	 * @return string
	 */
	public function getOrientamento() : string{
		return $this->orientamento;
	}

	/**
	 * @return string
	 */
	public function getPunti() : string{
		return $this->punti;
	}
}
