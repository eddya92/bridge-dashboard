<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface PrivacyRepository{
	public const TAG_PRIVACY = 'privacy';

	public function getPrivacy() : ?Generator;

	public function aggiornaDatiPrivacy(string $campo, string $valore, string $ip) : array;
}
