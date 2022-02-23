<?php
declare(strict_types=1);

namespace App\Widget\Carrello;

use App\Repository\CarrelloRepository;
use Twig\Environment;

class Carrello{
	public function __construct(
		private Environment        $twig,
		private CarrelloRepository $repository
	){
	}

	/**
	 * @return string
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function main(string $locale) : string{
		$carrelloEntity = $this->repository->getCarrello($locale);

		if($carrelloEntity == null || !$carrelloEntity->getArticoli()){
			$carrello = [];
		}elseif($carrelloEntity->getArticoli()){
			$carrello = [];
			$carrello[] = $carrelloEntity;
		}

		return $this->twig->render('widgets/carrello/carrello_header.html.twig',
			[
				'carrello' => $carrello,
			]);
	}
}
