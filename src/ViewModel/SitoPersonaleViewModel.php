<?php
declare(strict_types=1);

namespace App\ViewModel;

final class SitoPersonaleViewModel{
	/**
	 * @param string $fotoSfondo
	 * @param string $fotoProfilo
	 * @param string $titolo
	 * @param string $descrizione
	 * @param string $telefono
	 * @param string $email
	 * @param string $facebook
	 * @param string $instagram
	 * @param string $twitter
	 * @param string $youtube
	 * @param string $qualifica
	 * @param int    $collaboratori
	 * @param string $uri
	 */
	public function __construct(private string $fotoSfondo, private string $fotoProfilo, private string $titolo, private string $descrizione, private string $telefono, private string $email, private string $facebook, private string $instagram, private string $twitter, private string $youtube, private string $qualifica, private int $collaboratori, private string $uri){
	}

	/**
	 * @return string
	 */
	public function getFotoSfondo() : string{
		return $this->fotoSfondo;
	}

	/**
	 * @return string
	 */
	public function getFotoProfilo() : string{
		return $this->fotoProfilo;
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
	public function getDescrizione() : string{
		return $this->descrizione;
	}

	/**
	 * @return string
	 */
	public function getTelefono() : string{
		return $this->telefono;
	}

	/**
	 * @return string
	 */
	public function getEmail() : string{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getFacebook() : string{
		return $this->facebook;
	}

	/**
	 * @return string
	 */
	public function getInstagram() : string{
		return $this->instagram;
	}

	/**
	 * @return string
	 */
	public function getTwitter() : string{
		return $this->twitter;
	}

	/**
	 * @return string
	 */
	public function getYoutube() : string{
		return $this->youtube;
	}

	/**
	 * @return string
	 */
	public function getQualifica() : string{
		return $this->qualifica;
	}

	/**
	 * @return int
	 */
	public function getCollaboratori() : int{
		return $this->collaboratori;
	}

	/**
	 * @return string
	 */
	public function getUri() : string{
		return $this->uri;
	}
}
