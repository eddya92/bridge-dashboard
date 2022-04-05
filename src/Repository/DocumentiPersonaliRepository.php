<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface DocumentiPersonaliRepository{
	public const TAG_DOCUMENTI_PERSONALI = 'documentiPersonali';

	public function getDocumentiPersonali(string $locale) : Generator;

	public function caricaDocumentoPersonale(string $iddoc, string $base64doc, string $namedoc) : array;

	public function creaTesserino() ;
}
