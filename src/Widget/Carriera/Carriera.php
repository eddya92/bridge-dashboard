<?php
declare(strict_types=1);

namespace App\Widget\Carriera;

use App\Repository\AccountRepository;
use App\Repository\CarrieraPersonaleRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class Carriera{
	public function __construct(private Environment $twig, private CarrieraPersonaleRepository $carrieraPersonaleRepository
	){
	}

	/**
	 * @return string
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function main(string $codice, string $locale) : string{
		$cariera = $this->carrieraPersonaleRepository->getCarriera($locale);

		$messaggio = "widget ancora da definire";

		$carriera = [];
		$carriera['qualifica'] = 'AFFILIATO';
		$carriera['qualificaSuccessiva'] = [
			'isRaggiunto' => false,
			'nome'        => 'BUSINESS UNIT',
			'fast'        => [
				'idRaggiunto'    => false,
				'coins'          => 1000,
				'coinsPersonali' => '',
			],
			'low'         => [
				'idRaggiunto'    => false,
				'coins'          => '',
				'coinsPersonali' => '',
			],
		];

		if($cariera != null){
		//	return $this->twig->render('widgets/carriera/carriera_BU.html.twig', [
		//		'carriera' => $carriera,
		//	]);
		//	return $this->twig->render('widgets/carriera/carriera_conferma_BU.html.twig', [
		//		'carriera' => $carriera,
		//	]);
			return $this->twig->render('widgets/carriera/carriera.html.twig', [
				'carriera' => $carriera,
			]);
		}
	}
}
