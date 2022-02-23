<?php
declare(strict_types=1);

namespace App\ViewModel;

final class SaldoViewModel{
	/**
	 * @param float  $saldo
	 * @param float  $saldoMinimo
	 * @param array  $requisiti
	 * @param string $numeroFattura
	 * @param bool   $accrediti
	 * @param string $dataUltimoAccredito
	 * @param string $ultimoAggiornamento
	 * @param string $dataFattura
	 * @param bool   $autorizzazioneRichiestaCashout
	 * @param string $messaggioAutorizzazione
	 * @param bool   $requisitiSoddisfatti
	 */
	public function __construct(private float $saldo, private float $saldoMinimo, private array $requisiti, private string $numeroFattura, private bool $accrediti, private string $dataUltimoAccredito, private string $ultimoAggiornamento, private string $dataFattura, private bool $autorizzazioneRichiestaCashout, private string $messaggioAutorizzazione, private bool $requisitiSoddisfatti){
	}

	/**
	 * @return float
	 */
	public function getSaldo() : float{
		return $this->saldo;
	}

	/**
	 * @return float
	 */
	public function getSaldoMinimo() : float{
		return $this->saldoMinimo;
	}

	/**
	 * @return array
	 */
	public function getRequisiti() : array{
		return $this->requisiti;
	}

	/**
	 * @return string
	 */
	public function getNumeroFattura() : string{
		return $this->numeroFattura;
	}

	/**
	 * @return bool
	 */
	public function isAccrediti() : bool{
		return $this->accrediti;
	}

	/**
	 * @return string
	 */
	public function getDataUltimoAccredito() : string{
		return $this->dataUltimoAccredito;
	}

	/**
	 * @return string
	 */
	public function getUltimoAggiornamento() : string{
		return $this->ultimoAggiornamento;
	}

	/**
	 * @return string
	 */
	public function getDataFattura() : string{
		return $this->dataFattura;
	}

	/**
	 * @return bool
	 */
	public function isAutorizzazioneRichiestaCashout() : bool{
		return $this->autorizzazioneRichiestaCashout;
	}

	/**
	 * @return string
	 */
	public function getMessaggioAutorizzazione() : string{
		return $this->messaggioAutorizzazione;
	}

	/**
	 * @return bool
	 */
	public function isRequisitiSoddisfatti() : bool{
		return $this->requisitiSoddisfatti;
	}
}
