<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface DocumentiPersonaliRepository{
	public const TAG_DOCUMENTI_PERSONALI = 'documentiPersonali';

	/**
	 * Api che ci torna l'elenco dei documenti personali dell'utente loggatoù
	 *
	 * @param string $locale
	 *
	 * @return \Generator
	 */
	public function getDocumentiPersonali(string $locale) : Generator;

	/**
	 * Api che permette all'utente di caricare un doumento
	 *
	 * @param string $iddoc
	 * @param string $base64doc
	 * @param string $namedoc
	 *
	 * @return array
	 */
	public function caricaDocumentoPersonale(string $iddoc, string $base64doc, string $namedoc) : array;

	/**
	 * @return mixed
	 */
	public function creaTesserino() ;
}
