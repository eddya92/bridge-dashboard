<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface ModalitaSpedizioneRepository{
	public const TAG_MODALITA_SPEDIZIONE = 'modalitaSpedizione';

	public function getModalitaSpedizione(int $id_spedizione = 0) : ?Generator;
}
