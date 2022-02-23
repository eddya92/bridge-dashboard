<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface FilesRepository{
	public const TAG_FILES = 'files';

	public function getDocumenti(string $locale) : ?Generator;
}
