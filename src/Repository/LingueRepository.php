<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface LingueRepository{
	public const TAG_LINGUE = 'lingue';

	public function getLingue() : ?Generator;
}

