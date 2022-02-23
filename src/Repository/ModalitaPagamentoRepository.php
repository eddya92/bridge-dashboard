<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface ModalitaPagamentoRepository{
	public const TAG_MODALITA_PAGAMENTO = 'modalitaPagamento';

	public function getModalitaPagamento(int $id_spedizione = 0, int $id_modsped = 0) : ?Generator;
}
