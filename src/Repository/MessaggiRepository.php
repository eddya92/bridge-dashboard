<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\MessaggioViewModel;
use Generator;

interface MessaggiRepository{
	public const TAG_MESSAGGGI = 'messaggi';

	public function getMessaggi(string $_locale) : ?Generator;

	public function getMessaggio(int $id, string $_locale) : ?MessaggioViewModel;
}
