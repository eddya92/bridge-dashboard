<?php
declare(strict_types=1);

namespace App\Widget\Shop;

use App\Repository\ArticoliRepository;
use App\Repository\FiltriRepository;
use Twig\Environment;

final class Shop{
	public function __construct(private Environment $twig, private ArticoliRepository $repository, private FiltriRepository $categorierepository){
	}

	/**
	 * @return string
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function main($value) : string{
		return $this->twig->render('widgets/shop/shop.html.twig',
			[
				'articles' => $this->repository->getArticoli($value),
			]);
	}

	public function ajax(string $locale, $filtriAttivi) : string{
		$articoli = $this->repository->getArticoli($locale, $filtriAttivi);

		if($articoli != null){
			return $this->twig->render('widgets/shop/shop.html.twig',
				[
					'articoli'     => $articoli,
					'filtriAttivi' => $filtriAttivi,
				]);
		}else{
			return 'Non ci sono articoli per questi filtri.';
		}
	}

	public function articoloShop() : string{
		return $this->twig->render('widgets/shop/shop.html.twig',
		);
	}

	public function filtriShop(string $_locale) : string{
		$categorieEntity = $this->categorierepository->getCategorieFiltri($_locale);

		$categorie = [];
		if($categorieEntity != null){
			$categorie = $categorieEntity;
		}

		return $this->twig->render('widgets/shop/filtri_shop.html.twig',
			[
				'categorie' => $categorie,
			]);
	}
}
