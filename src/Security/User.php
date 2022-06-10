<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use function array_unique;

final class User implements UserInterface{
	public function __construct(private string $token, private array $roles, private string $superiore, private string $codice, private string $nominativo, private string $qualifica, private string $id_ruolo, private string $foto, private int $articoliCarrello, private string $locale){
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
	public function getQualifica() : string{
		return $this->qualifica;
	}

	/**
	 * @return string
	 */
	public function getToken() : string{
		return $this->token;
	}

	/**
	 * @return string
	 */
	public function getSuperiore() : string{
		return $this->superiore;
	}

	/**
	 * @return string
	 */
	public function getCodice() : string{
		return $this->codice;
	}

	public function getUsername() : string{
		return $this->getUserIdentifier();
	}

	public function getUserIdentifier() : string{
		return $this->token;
	}

	public function getFoto() : string{
		return $this->foto;
	}

	public function getArticoliCarrello() : int{
		return $this->articoliCarrello;
	}

	public function getPassword() : ?string{
		return null;
	}

	public function getSalt() : ?string{
		return null;
	}

	public function getLocale() : string{
		return $this->locale;
	}

	public function eraseCredentials(){
	}

	/**
	 * @return string
	 */
	public function getIdRuolo() : string{
		return $this->id_ruolo;
	}

	public function getRoles() : array{
		$roles = $this->roles;
		$roles[] = 'ROLE_USER';
		$roles[] = 'ROLE_USER_' . $this->id_ruolo;

		return array_unique($roles);
	}
}
