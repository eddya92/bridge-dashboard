<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\CarrelloViewModel;
use App\ViewModel\CarrelloTotaliViewModel;

interface CarrelloRepository{
	public const TAG_CARRELLO = 'carrello';

	public function getCarrello(string $_locale) : ?CarrelloViewModel;

	public function apriNuovoCarrello(string $store, string $listino, string $id_tipo_ordine) : array;

	public function aggiungiArticolo(int $ID_articolo, int $ID_variante = 0, int $quantita = 0) : array;

	public function aggiornaArticolo(int $ID_articolo, int $ID_variante = 0, int $quantita = 0) : array;

	public function eliminaArticolo(int $ID_articolo, int $ID_variante = 0) : array;

	public function getTotali(string $_locale, int $id_spedizione = 0, int $id_modsped = 0, int $id_modpag = 0) : ?CarrelloTotaliViewModel;

	public function checkout(array $parametri) : array;
}
