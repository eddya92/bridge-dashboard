<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\FilesRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller per la gestione della pagina ALBERO CONTROLLER
 */
class AlberoDocumentiController extends AbstractController{
	/**
	 * @param \App\Repository\FilesRepository $repository
	 */
	public function __construct(
		private FilesRepository $repository,
	){
	}

	/**
	 * Carica nella pagina documenti, tutti i file necessari per la visualizzazione, prendendo la prima directory, che verrà cliccato in ajax per mostrare i documenti al suo interno
	 */
	#[Route('/{_locale}/documenti', name: 'documenti', methods: ['GET'])]
	public function documenti($_locale) : Response{
		//region 1. faccio chiamata api,se viene lanciata eccezione vado nella home con messaggio di errore
		try{
			$documentiTotali = $this->repository->getDocumenti($_locale);
		}catch(Exception $exception){
			$this->addFlash('error', $exception->getCode() . " : " . $exception->getMessage());

			return $this->redirectToRoute('ingresso');
		}
		//endregion


		$documenti = [];
		foreach($documentiTotali as $documento){
			/** @var $documento \App\ViewModel\DocumentoFileViewModel */
			if($documento->isCartella()){
				$documenti[] = $documento;
			}
		}

		$idPrimo = $documenti[0]->getId();

		return $this->render(
			'pages/albero_documenti/documenti.html.twig'
			, [
				'documenti' => $documenti,
				'idPrimo'   => $idPrimo,
			]
		);
	}
}