<?php
declare(strict_types=1);

namespace App\Widget\GraficoVendite;

use App\Service\LocaleDateFormat;
use DateTime;
use Twig\Environment;

final class GraficoVendite{
	public function __construct(
		private Environment $twig
	){
	}

	public function main($locale) : string{
		$annoAttuale = date('Y');
		$meseAttuale = date('m');
		$dataInizio = date('2020-01-01');

		list($anno, $mese, $giorno) = explode('-', $dataInizio);
		$t_stamp_1 = '';
		$mese = (int) $mese;
		$giorno = (int) $giorno;
		$anno = (int) $anno;

		$dateFormat = new LocaleDateFormat('MMMM'); // nome mese per esteso esempio => 'gennaio'

		$date = new DateTime(); # Now
		$arrayMesi = [];
		$key = 0;

		//prendo i mesi dell 'anno e li formatto come mi serve '01' => 'Gennaio'
		foreach(range(01, 12) as $monthNumber){
			$arraynumeroMesi = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
			$key++;
			$date->setDate($anno, $monthNumber, 1);

			$monthLabel = $dateFormat->localeFormat($locale, $date);
			$arrayMesi[$arraynumeroMesi[$key]] = ucfirst($monthLabel);//push mese per esteso + numero mese es. '01' => 'Gennaio'

		}

		$arrayAnni = [];


		for($i = $anno; (int) $annoAttuale >= $i; $i++){
			array_push($arrayAnni, $i);
		}

		return $this->twig->render('widgets/grafico_vendite/grafico_vendite.html.twig', [
			'annoAttuale' => $annoAttuale,
			'meseAttuale' => $meseAttuale,
			'anni'        => $arrayAnni,
			'mesi'        => $arrayMesi,
		]);
	}
}
