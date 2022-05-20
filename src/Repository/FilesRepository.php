<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface FilesRepository{
	public const TAG_FILES = 'files';

	/**
	 * Genera l'elenco delle cartelle per creare l'albero dei documenti
	 *
	 * @param string $locale
	 *
	 * @return \Generator|null
	 */
	public function getDocumenti(string $locale) : Generator;
}
