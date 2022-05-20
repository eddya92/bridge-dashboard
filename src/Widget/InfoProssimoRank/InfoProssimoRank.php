<?php
declare(strict_types=1);

namespace App\Widget\InfoProssimoRank;

use App\Repository\CarrieraPersonaleRepository;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function dd;

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

		return $this->twig->render('widgets/carriera/carriera.html.twig', [
			'info' => $info,
		]);
	}
}
