<?php
declare(strict_types=1);

namespace App\Controller\Widget\Agreements;

use App\Repository\NazioniRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Aggiorna i documenti da accettare in fase di registrazione,in base alla nazione scelta.
 */
final class AgreementsController extends AbstractController{
	public function __construct(
		private NazioniRepository $agreementsRepository
	){
	}

	/**
	 *  Aggiorna i documenti da accettare in fase di registrazione, in base alla nazione scelta.
	 */
	#[Route('/ajax-agreements/{idNazione}', name: 'ajax-agreements', methods: ['GET'])]
	public function ajaxAgreements(string $idNazione) : Response{
		$agreements = $this->agreementsRepository->getAgreements($idNazione);

		if($agreements == null){
			$agreements = [];
		}

		$agreements_nazione = [];
		foreach ($agreements as $agreement) {
			$agreements_nazione[] = $agreement;
		}

		return $this->render('widgets/agreements/agreements_body_ajax.html.twig', [
			'agreements' => $agreements_nazione,
		]);
	}
}
