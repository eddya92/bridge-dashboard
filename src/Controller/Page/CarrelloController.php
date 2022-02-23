<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\CarrelloRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CarrelloController extends AbstractController{
	public function __construct(
		private CarrelloRepository $repository,
	){
	}

	#[Route('{_locale}/carrello', name: 'carrello', methods: ['GET'])]
	public function carrello($_locale){
		$carrello = $this->repository->getCarrello($_locale);

		if($carrello == null){
			$this->addFlash('success', 'Carrello non trovato, inizia ora gli acquisti.');

			return $this->redirectToRoute('crea-nuovo-ordine');
		}elseif(!$carrello->getArticoli()){
			return $this->render('pages/carrello/carrello_vuoto.html.twig');
		}

		return $this->render('pages/carrello/carrello.html.twig',
			[
				'carrello' => $carrello,
			]);
	}

	#[Route('/aggiungi-articolo', name: 'aggiungi-articolo', methods: ['GET', 'POST'])]
	public function aggiungiArticolo(Request $request){
		$ID_articolo = (int) $request->query->get('id', 0);
		$ID_variante = (int) $request->query->get('idv', 0);
		$quantita = (int) $request->query->get('qty', 0);

		[, $errors] = $this->repository->aggiungiArticolo($ID_articolo, $ID_variante, $quantita);
		if($errors != ''){
			$this->addFlash('error', $errors);
		}else{
			$this->addFlash('success', 'Articolo aggiunto al carrello.');
		}

		return $this->redirectToRoute('carrello');
	}

	#[Route('/aggiorna-articolo', name: 'aggiorna-articolo', methods: ['GET'])]
	public function aggiornaArticolo(Request $request){
		$ID_articolo = (int) $request->query->get('id', 0);
		$ID_variante = (int) $request->query->get('idv', 0);
		$quantita = (int) $request->query->get('qty', 0);

		[, $errors] = $this->repository->aggiornaArticolo($ID_articolo, $ID_variante, $quantita);
		if($errors != ''){
			$this->addFlash('error', $errors);
		}else{
			$this->addFlash('success', 'Carello aggiornato.');
		}

		return $this->redirectToRoute('carrello');
	}

	#[Route('/elimina-articolo', name: 'elimina-articolo', methods: ['GET'])]
	public function eliminaArticolo(Request $request){
		$ID_articolo = (int) $request->query->get('id', 0);
		$ID_variante = (int) $request->query->get('idv', 0);

		[, $errors] = $this->repository->eliminaArticolo($ID_articolo, $ID_variante);
		if($errors != ''){
			$this->addFlash('error', $errors);
		}else{
			$this->addFlash('success', 'Articolo rimosso dal carrello.');
		}

		return $this->redirectToRoute('carrello');
	}
}