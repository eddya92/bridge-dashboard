<?php
declare(strict_types=1);

namespace App\Widget\Lingue;

use App\Repository\LingueRepository;
use Twig\Environment;

final class Lingue{
	public function __construct(private Environment $twig, private LingueRepository $repository){
	}

	public function main() : string{
		$lingue = $this->repository->getLingue();
		if($lingue === null){
			return $this->twig->render('pages/error_widgets/errore_widget_vuoto.html.twig');
		};

		return $this->twig->render('widgets/lingue/lingue.html.twig', ['lingue' => $lingue,]);
	}

}
