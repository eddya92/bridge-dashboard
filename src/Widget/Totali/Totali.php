<?php
declare(strict_types=1);

namespace App\Widget\Totali;

use App\Repository\TotaliRepository;
use Twig\Environment;

final class Totali{
	public function __construct(private Environment $twig, private TotaliRepository $repository){
	}

	public function main(string $utenza, string $locale) : string{
		$totali = $this->repository->getTotali($utenza, $locale);

		if($totali === null){
			return $this->twig->render('pages/error_widgets/errore_widget_vuoto.html.twig');
		};

		//return $this->twig->render('pages/errori/errore_widget_vuoto.html.twig');

		return $this->twig->render('widgets/totali/totali.html.twig', [
			'totali' => $totali,
		]);
	}
}
