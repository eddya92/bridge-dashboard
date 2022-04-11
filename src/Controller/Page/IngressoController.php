<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\ArticoliRepository;
use App\Repository\KitsRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller della pagina d'ingresso
 */
final class IngressoController extends AbstractController{
	use AuthenticatedConnectionCapability;

	/**
	 * @param \App\Repository\AccountRepository  $accountRepository
	 * @param \App\Repository\ArticoliRepository $articleRepository
	 * @param \App\Repository\KitsRepository     $kitrepository
	 */
	public function __construct(
		private AccountRepository  $accountRepository,
		private ArticoliRepository $articleRepository,
		private KitsRepository     $kitrepository
	){
	}

	/**
	 * Vista della pagina ingresso, controlla se sei un cliente o un incaricato, a seconda di questo ti mostra determinate cose
	 */
	#[Route('/', name: 'index')]
	public function index() : Response{
		return $this->redirectToRoute('ingresso');
	}

	/**
	 * Vista della pagina ingresso, controlla se sei un cliente o un incaricato, a seconda di questo ti mostra determinate cose
	 */
	#[Route('/{_locale}/ingresso{utenza}', name: 'ingresso', methods: ['GET'], defaults: ['utenza' => ''])]
	public function ingresso(string $_locale, Request $request, string $utenza) : Response{
		if($utenza){
			$account = $this->accountRepository->getAccount($utenza, $_locale);
		}else{
			$account = $this->accountRepository->getAccount($this->getUser()->getCodice(), $_locale);
		}
		$superiore = $account->getSuperiore();

		if($account == null){
			return $this->redirectToRoute('logout');
		}

		//region controllo il ruolo dell'utente loggato, se è clinte mostro una pagina con la possibilità di trasformarsi in incaricato
		if($account->getRuolo() === 'Cliente'){
			$superiore = $this->getUser()->getSuperiore();
			$ultimiArticoliVenduti = $this->articleRepository->getArticoliUltimiAcquisti($_locale);
			$articoliPiuVenduti = $this->articleRepository->getArticoliPiuVenduti($_locale);

			if($ultimiArticoliVenduti == null){
				$ultimiArticoliVenduti = [];
			}

			if($articoliPiuVenduti == null){
				$articoliPiuVenduti = [];
			}

			if($superiore == null){
				$superiore = [];
			}

			return $this->render(
				'pages/ingresso/ingresso_cliente.html.twig',
				[
					'account'               => $account,
					'superiore'             => $superiore,
					'ultimiArticoliVenduti' => $ultimiArticoliVenduti,
					'articoliPiuVenduti'    => $articoliPiuVenduti,
				]
			);
		}else{
			$utenza = $request->get('utenza', '');

			return $this->render(
				'pages/ingresso/ingresso.html.twig',
				[
					'account'   => $account,
					'superiore' => $superiore,
					'utenza'    => $utenza,
				]
			);
		}
	}
}
