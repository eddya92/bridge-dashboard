<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\FilesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
	 * Carica nella pagina documenti, tutti i file necessari per la visualizzazione, prendendo il primo file, che verrà cliccato in ajax per mostrare i documenti al suop interno
	 */
	#[Route('/{_locale}/documenti', name: 'documenti', methods: ['GET'])]
	public function documenti($_locale) : Response{
		$documentiTotali = $this->repository->getDocumenti($_locale);

		$documenti = [];

		if($documentiTotali != null){
			foreach($documentiTotali as $documento){
				/** @var $documento \App\ViewModel\DocumentoFileViewModel */
				if($documento->isCartella()){
					$documenti[] = $documento;
				}
			}

			$idPrimo = $documenti[0]->getId();
		}else{
			$idPrimo = [];
		}

		return $this->render(
			'pages/albero_documenti/documenti.html.twig'
			, [
				'documenti' => $documenti,
				'idPrimo'   => $idPrimo,
			]
		);
	}
}