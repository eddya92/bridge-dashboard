<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface StoresRepository
{
	public const TAG_STORES = 'stores';

    public function getStore(): ?Generator;

}
