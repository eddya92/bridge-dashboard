<?php
declare(strict_types=1);

namespace App\ViewModel;

final class EmailTemplateViewModel{
	/**
	 * @param int    $id
	 * @param string $foto
	 * @param string $intestazione
	 * @param string $oggetto
	 * @param string $email
	 * @param int    $email_1h
	 * @param int    $email_1d
	 * @param int    $email_1w
	 * @param int    $email_1m
	 * @param int    $email_1h_totali
	 * @param int    $email_1d_totali
	 * @param int    $email_1w_totali
	 * @param int    $email_1m_totali
	 */
	public function __construct(private int $id, private string $foto, private string $intestazione, private string $oggetto, private string $email, private int $email_1h, private int $email_1d, private int $email_1w, private int $email_1m, private int $email_1h_totali, private int $email_1d_totali, private int $email_1w_totali, private int $email_1m_totali){
	}

	/**
	 * @return string
	 */
	public function getIntestazione() : string{
		return $this->intestazione;
	}

	/**
	 * @return int
	 */
	public function getEmail1hTotali() : int{
		return $this->email_1h_totali;
	}

	/**
	 * @return int
	 */
	public function getEmail1dTotali() : int{
		return $this->email_1d_totali;
	}

	/**
	 * @return int
	 */
	public function getEmail1wTotali() : int{
		return $this->email_1w_totali;
	}

	/**
	 * @return int
	 */
	public function getEmail1mTotali() : int{
		return $this->email_1m_totali;
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
	public function getFoto() : string{
		return $this->foto;
	}

	/**
	 * @return string
	 */
	public function getOggetto() : string{
		return $this->oggetto;
	}

	/**
	 * @return string
	 */
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @return int
	 */
	public function getEmail1h() : int{
		return $this->email_1h;
	}

	/**
	 * @return int
	 */
	public function getEmail1d() : int{
		return $this->email_1d;
	}

	/**
	 * @return int
	 */
	public function getEmail1w() : int{
		return $this->email_1w;
	}

	/**
	 * @return int
	 */
	public function getEmail1m() : int{
		return $this->email_1m;
	}

	/**
	 * @return int
	 */
	public function getEmailTotali() : int{
		return $this->email_totali;
	}
}
