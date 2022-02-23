<?php
declare(strict_types=1);

namespace App\ViewModel;

final class MessaggioViewModel{
	/**
	 * @param int    $id
	 * @param string $data
	 * @param string $mittente
	 * @param string $foto
	 * @param bool   $da_leggere
	 * @param string $titolo
	 * @param string $testo
	 * @param int    $id_messaggio_precedente
	 * @param int    $id_messaggio_successivo
	 */
	public function __construct(private int $id, private string $data, private string $mittente, private string $foto, private bool $da_leggere, private string $titolo, private string $testo, private int $id_messaggio_precedente, private int $id_messaggio_successivo){
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
	public function getData() : string{
		return $this->data;
	}

	/**
	 * @return string
	 */
	public function getMittente() : string{
		return $this->mittente;
	}

	/**
	 * @return string
	 */
	public function getFoto() : string{
		return $this->foto;
	}

	/**
	 * @return bool
	 */
	public function isDaLeggere() : bool{
		return $this->da_leggere;
	}

	/**
	 * @return string
	 */
	public function getTitolo() : string{
		return $this->titolo;
	}

	/**
	 * @return string
	 */
	public function getTesto() : string{
		return $this->testo;
	}
}
