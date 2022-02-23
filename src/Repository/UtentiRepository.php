<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface UtentiRepository{
	public const TAG_UTENTI = 'utenti';

	public function getUtente(string $cerca = '') : Generator;
}
