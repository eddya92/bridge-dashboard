<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\CarrelloRepository;
use App\Repository\FiltriRepository;
use App\Repository\ArticoliRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gestione della pagina relativa allo shop
 */
class ShopController extends AbstractController{
	/**
	 * @param \App\Repository\ArticoliRepository $repository
	 * @param \App\Repository\FiltriRepository   $filtriRepository
	 * @param \App\Repository\CarrelloRepository $carrelloRepository
	 */
	public function __construct(
		private ArticoliRepository $repository,
		private FiltriRepository   $filtriRepository,
		private CarrelloRepository $carrelloRepository
	){
	}

	/**
	 * Vista dello shop
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/shop', name: 'shop', methods: ['GET'])]
	public function shop() : Response{
		return $this->render('pages/shop/shop.html.twig');
	}

	/**
	 * Caricamento
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/shop-ajax', name: 'shop-ajax', methods: ['POST', 'GET'])]
	public function shopAjax(){
		$request = Request::createFromGlobals();
		$filtriAttivi = $request->request->all();

		return $this->render('pages/shop/shop_ajax.html.twig', [
			"filtriAttivi" => $filtriAttivi,
		]);
	}

	/**
	 * Vista dettaglio di un singolo articolo
	 *
	 */
	#[Route('{_locale}/articolo/{id}', name: 'articolo', methods: ['GET'])]
	public function articolo(string $_locale, int $id) : Response{
		$articolo = $this->repository->getArticolo($_locale, $id);
		if($articolo != null){
			$carrello = $this->carrelloRepository->getCarrello($_locale);

			return $this->render('pages/shop/shop_articolo.html.twig',
				[
					'articolo' => $articolo,
					'carrello' => $carrello,
				]);
		}else{
			$this->addFlash('error', 'Articolo non trovato.');

			return $this->redirectToRoute('shop');
		}
	}

	/**
	 * Chiamata Api dei filtri
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	#[Route('/filtri-ajax/{locale}', name: 'filtri-ajax', methods: ['GET'])]
	public function filtriAjax(string $locale) : JsonResponse{

		$categoriegenerator = json_decode($this->json($this->filtriRepository->getCategorieFiltri($locale))->getContent());

		if($categoriegenerator != null){
			return $this->json($categoriegenerator);
		}else{
			return $this->json([]);
		}
	}
}