<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller gestione pagina Ewallet
 */
class EWalletController extends AbstractController{
	/**
	 * Visualizzazione pagina ewallet
	 */
	#[Route('/ewallet', name: 'ewallet', methods: ['GET'])]
	public function ewallet() : Response{
		return $this->render('pages/ewallet/ewallet.html.twig');
	}

	/**
	 * Invio richiesta cashout
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/cashout', name: 'cashout', methods: ['POST'])]
	public function cashout(Request $request) : RedirectResponse{
		//dd($request->request->all());
		$this->addFlash(
			'success',
			'la tua richiesta è stata inviata correttamente'
		);

		$importo = $request->get('importo');
		$numeroDocumento = $request->get('numero_documento');
		$dataDocumento = $request->get('data_documento');

		$all = $request->request->all();

		return $this->redirectToRoute('ewallet');
	}
}