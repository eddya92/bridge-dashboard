<?php
declare(strict_types=1);

namespace App\Widget\MySponsor;

use App\Repository\SponsorRepository;
use Twig\Environment;

final class Mysponsor{
	public function __construct(private Environment $twig, private SponsorRepository $repository){
	}

	public function main() : string{
		$sponsor = $this->repository->getSponsor();
		if($sponsor != null){
			return $this->twig->render('widgets/sponsor/sponsor.html.twig',
				[
					'sponsor' => $sponsor,
				]);
		}else{
			return $this->twig->render('widgets/sponsor/sponsor_errore.html.twig',
			);
		}
	}
}
