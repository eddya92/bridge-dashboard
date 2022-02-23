<?php
declare(strict_types=1);

namespace App\ViewModel;

final class Top5UserViewModel{
	/**
	 * @param string $categoria
	 * @param string $nome
	 * @param string $cognome
	 * @param string $nominativo
	 * @param string $codice
	 * @param string $foto
	 * @param string $dataIscrizione
	 * @param int    $nCollaboratori
	 * @param int    $ordini
	 */
	public function __construct(private string $categoria, private string $nome, private string $cognome, private string $nominativo, private string $codice, private string $foto, private string $dataIscrizione, private int $nCollaboratori, private int $ordini){
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
	public function getNome() : string{
		return $this->nome;
	}

	/**
	 * @return string
	 */
	public function getCognome() : string{
		return $this->cognome;
	}

	/**
	 * @return string
	 */
	public function getNominativo() : string{
		return $this->nominativo;
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
	public function getFoto() : string{
		return $this->foto;
	}

	/**
	 * @return string
	 */
	public function getDataIscrizione() : string{
		return $this->dataIscrizione;
	}

	/**
	 * @return int
	 */
	public function getNCollaboratori() : int{
		return $this->nCollaboratori;
	}

	/**
	 * @return int
	 */
	public function getOrdini() : int{
		return $this->ordini;
	}


}
