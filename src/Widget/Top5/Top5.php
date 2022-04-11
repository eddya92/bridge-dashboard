<?php
declare(strict_types=1);

namespace App\Widget\Top5;

use Twig\Environment;

/**
 *nel primo caricamento della pagina,vengono inseriti dati con chiamata api del mese corrente
 * al cambio dell'anno o mese viene cambiato html(twig) con una nuova chiamata
 * per questo sono stati creati tanti metodi
 */
final class Top5{
	public function __construct(private Environment $twig){
	}

	public function main(string $utenza) : string{
		$annoAttuale = date('Y');
		$meseAttuale = date('m');
		$dataInizio = date('2020-01-01');

		list($anno, $mese, $giorno) = explode('-', $dataInizio);
		$t_stamp_1 = '';
		$mese = (int) $mese;
		$giorno = (int) $giorno;
		$anno = (int) $anno;

		$arrayAnni = [];
		$arrayMesi = [
			'01' => 'Gennaio',
			'02' => 'Febbraio',
			'03' => 'Marzo',
			'04' => 'Aprile',
			'05' => 'Maggio',
			'06' => 'Giugno',
			'07' => 'Luglio',
			'08' => 'Agosto',
			'09' => 'Settembre',
			'10' => 'Ottobre',
			'11' => 'Novembre',
			'12' => 'Dicembre',
		];

		for($i = $anno; (int) $annoAttuale >= $i; $i++){
			array_push($arrayAnni, $i);
		}

		return $this->twig->render('widgets/top5/top5.html.twig', [
			'annoAttuale' => $annoAttuale,
			'meseAttuale' => $meseAttuale,
			'anni'        => $arrayAnni,
			'mesi'        => $arrayMesi,
			'utenza'      => $utenza,
		]);
	}
}
