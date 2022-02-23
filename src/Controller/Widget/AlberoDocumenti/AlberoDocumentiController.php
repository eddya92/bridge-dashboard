<?php
declare(strict_types=1);

namespace App\Controller\Widget\AlberoDocumenti;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Aggiorna i file visualizati, in base alla cartella scelta
 */
final class AlberoDocumentiController extends AbstractController{
	public function __construct(){
	}

	/**
	 * Aggiorna i file visualizati, in base alla cartella scelta
	 */
	#[Route('/albero-documenti-ajax/{id_cartella}', name: 'albero-documenti-ajax', methods: ['GET'])]
	public function alberoDocumentiAjax(int $id_cartella) : Response{
		return $this->render('pages/albero_documenti/albero_documenti.html.twig', [

				'id_cartella' => $id_cartella,
			]
		);
	}
}