<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\AlberoUnilevelVistaViewModel;

interface ReteRepository{
	public const TAG_VISTE_ALBERI = 'visteAlbero';
	public const TAG_ALBERI = 'alberi';

	/**
	 * Restituisce l'elenco delle Viste dell'utente loggato (la Default + eventuali altre salvate)
	 *
	 * @param string $locale
	 *
	 * @return iterable<AlberoUnilevelVistaViewModel>
	 */
	public function allVisteUnilevelOfIdUtente(string $locale) : iterable;

	/**
	 * Restituisce un array contenente i nodi dell'albero Unilevel di $idUtente nel $Mese, in base ai filtri applicati
	 *
	 * @param int    $ID_utente
	 * @param string $Mese
	 * @param string $punti
	 * @param string $icon
	 * @param bool   $show_disattivi
	 * @param bool   $hide_nulli
	 * @param string $locale
	 *
	 * @return array
	 */
	public function unilevelTreeOfIdUtente(int $ID_utente, string $Mese, string $punti, string $icon, bool $show_disattivi, bool $hide_nulli, string $locale) : array;

	/**
	 * Elimina una Vista Salvata
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function eliminaAlberoVista(int $id);

	/**
	 * Salva una Vista (quella di Default se $ID_vista=0, una salvata se $ID_vista > 0)
	 *
	 * @param bool   $isDefault
	 * @param int    $ID_vista
	 * @param string $Nome
	 * @param int    $ID_utente_albero
	 * @param int    $max_livelli
	 * @param string $Mese
	 * @param int    $height
	 * @param int    $width
	 * @param string $orientamento
	 * @param string $icon
	 * @param string $punti
	 *
	 * @return array
	 */
	public function salvaAlberoVista(bool $isDefault, int $ID_vista, string $Nome, int $ID_utente_albero, int $max_livelli, string $Mese, int $height, int $width, string $orientamento, string $icon, string $punti) : array;
}
