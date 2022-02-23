<?php
declare(strict_types=1);

namespace App\ViewModel;

final class InquadramentoFiscaleViewModel{
	/**
	 * @param string $nome
	 * @param string $descrizione
	 * @param string $emailAzienda
	 * @param string $iban
	 * @param string $bankCode
	 */
	public function __construct(private string $nome, private string $descrizione, private string $emailAzienda, private string $iban, private string $bankCode){
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
	public function getEmailAzienda() : string{
		return $this->emailAzienda;
	}

	/**
	 * @return string
	 */
	public function getIban() : string{
		return $this->iban;
	}

	/**
	 * @return string
	 */
	public function getBankCode() : string{
		return $this->bankCode;
	}


}
