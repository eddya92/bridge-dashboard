<?php
declare(strict_types=1);

namespace App\Widget\NotiziePrincipali;

use App\Repository\MessaggiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class NotiziePrincipali extends AbstractController{
	/**
	 * @param \App\Repository\MessaggiRepository $messaggiRepository
	 */
	function __construct(
		private MessaggiRepository $messaggiRepository
	){
	}

	public function main(string $_locale) : Response{
		$messaggio = $this->messaggiRepository->getUltimoMessaggio($_locale);


		if($messaggio == null){
			$messaggio = [];
		}

		return $this->render('widgets/notizie_principali/notizie_principali.html.twig', [
			'messaggio' => $messaggio,
		]);
	}
}
