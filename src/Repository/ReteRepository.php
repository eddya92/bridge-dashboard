<?php
declare(strict_types=1);

namespace App\Repository;

use Generator;

interface ReteRepository{
	public const TAG_VISTE_ALBERI = 'visteAlbero';

	public function getAlberoViste() : ?Generator;

	public function getAlberoUnilevel(int $idUtente, int $livello, string $mese, string $punti ,int $idVista) : array;

	public function eliminaAlberoVista(int $id) ;

	public function salvaAlberoVista(int $principale, string $nome, int $idUtente, int $livelli, string $mese, string $altezza, string $larghezza, string $orientamento, string $punti, int $idVista) : array;
}
