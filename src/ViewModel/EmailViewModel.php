<?php
declare(strict_types=1);

namespace App\ViewModel;

final class EmailViewModel{
	/**
	 * @param string $data
	 * @param string $user
	 * @param string $email
	 */
	public function __construct(private string $data, private string $user, private string $email){
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
	public function getUser() : string{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getEmail() : string{
		return $this->email;
	}
}
