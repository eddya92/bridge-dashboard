<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\ArticoloViewModel;
use Generator;

interface ArticoliRepository{
	public const TAG_ARTICOLI = 'articoli';
	public const TAG_ARTICOLI_PIU_VENDUTI = 'articoliPiuVenduti';
	public const TAG_ARTICOLI_ULTIMI_ACQUISTI = 'articoliUltimiAcquisti';

	public function getArticoliPiuVenduti(string $_locale) : ?Generator;

	public function getArticoliUltimiAcquisti(string $_locale) : ?Generator;

	public function getArticolo(string $_locale, int $id) : ?ArticoloViewModel;

	public function getArticoli(string $locale, array $filtriAttivi) : ?Generator;
}
