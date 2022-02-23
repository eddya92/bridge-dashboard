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
		$messaggiGenerator = $this->messaggiRepository->getMessaggi($_locale);

		$messaggi = [];
		if($messaggiGenerator != null){
			foreach($messaggiGenerator as $messaggioGenerator){
				$messaggi[] = $messaggioGenerator;
			}
			rsort($messaggi);
		}

		if(count($messaggi) == 0){
			$messaggio = [];
		}else{
			$messaggio = $messaggi[0];
		}


		return $this->render('widgets/notizie_principali/notizie_principali.html.twig', [
			'messaggio' => $messaggio,
		]);
	}
}
