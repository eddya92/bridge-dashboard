<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\ContattiViewModel;

interface ContattiRepository{
	public const TAG_CONTATTI = 'contatti';

	public function getContatti(string $_locale) : ?ContattiViewModel;

	public function aggiornaContatti(string $telefono, string $cellulare) : array;
}
