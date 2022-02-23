<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\InquadramentoFiscaleViewModel;

interface InquadramentoFiscaleRepository{
	public const TAG_INQUADRAMENTO_FISCALE = 'inquadramentoFiscale';

	public function getInquadramentoFiscale(string $_locale) : ?InquadramentoFiscaleViewModel;

	public function aggiornaPagamenti(string $iban, string $bankCode) : array;
}
