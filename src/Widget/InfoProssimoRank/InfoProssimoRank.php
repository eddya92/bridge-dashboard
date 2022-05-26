<?php
declare(strict_types=1);

namespace App\Widget\InfoProssimoRank;

use App\Repository\CarrieraPersonaleRepository;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class InfoProssimoRank{
	public function __construct(private Environment $twig, private CarrieraPersonaleRepository $carrieraPersonaleRepository
	){
	}

	/**
	 * @param string $codice
	 * @param string $locale
	 *
	 * @return string
	 * @throws LoaderError|RuntimeError|SyntaxError
	 */
	public function main(string $codice, string $locale) : string{
		try{
			$info = $this->carrieraPersonaleRepository->infoProssimoRank($codice, $locale);
		}catch(Exception $exception){
			return $this->twig->render('widgets/carriera/carriera_error.html.twig', [
				'messaggio' => $exception->getCode() . " : " . $exception->getMessage(),
			]);
		}

		//region Controllo se ci sono obbiettiviRaggoiunti
		$almenoUnobbiettivoRaggiunto = false;
		foreach($info['rankSuccessivo']['soglie'] as $soglia){
			if($soglia['obiettivoRaggiunto']){
				$almenoUnobbiettivoRaggiunto = true;
				break;
			}
		}
		//endregion

		//region Qualifica PROMOTER(Che deve confermare la BU)
		if($info['rankAttuale']['Id'] === 49 && $almenoUnobbiettivoRaggiunto){
			return $this->twig->render('widgets/carriera/carriera_conferma_BU.html.twig', [
				'info' => $info,
			]);
		}
		//endregion

		//region Qualifica PROMOTER
		if($info['rankAttuale']['Id'] === 49){
			return $this->twig->render('widgets/carriera/carriera.html.twig', [
				'info' => $info,
			]);
		}
		//endregion

		//region Mantenimento Qualifiche
		if($info['rankAttuale']['Id'] === 86 || $info['rankAttuale']['Id'] === 95){
			return $this->twig->render('widgets/carriera/carriera_BU.html.twig', [
				'info' => $info,
			]);
		}
		//endregion

		return $this->twig->render('widgets/carriera/carriera_error.html.twig', [
			'messaggio' => 'Errore nel calcolo della qualifica,riprovare!',
		]);
	}
}
