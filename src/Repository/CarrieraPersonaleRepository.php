<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface CarrieraPersonaleRepository{
	public const TAG_CARRIERA_PERSONALE = 'carrieraPersonale';

	/**
	 * @param string $_locale
	 *
	 * @return \Generator|null
	 */
	public function getQualifiche(string $_locale) : Generator;

	/**
	 * Chiamata che restituisce le informazioni riguardanti il raggiungimento del prossimo rank e il mantenimento di quello attuale
	 *
	 * @param string $codice
	 * @param string $locale
	 *
	 * @return array
	 */
	public function infoProssimoRank(string $codice, string $locale) : array;

	/**
	 * Chiamata per la conferma della qualifica BU
	 *
	 * @return mixed
	 */
	public function confermaQualificaBU();
}
