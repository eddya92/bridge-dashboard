<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\SitoPersonaleViewModel;

interface SitoPersonaleRepository{
	public const TAG_SITO_PERSONALE = 'sitoPersonale';

	public function getSitoPersonale() : ?SitoPersonaleViewModel;

	public function aggiornaSitoPersonale(string $titolo, string $descrizione, string $telefono, string $email, string $facebook, string $instagram, string $twitter, string $youtube, array $immagine) : array;

	public function getMinisito(string $uri) : ?SitoPersonaleViewModel;
}
