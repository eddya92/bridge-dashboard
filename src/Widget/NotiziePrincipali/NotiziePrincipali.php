<?php
declare(strict_types=1);

namespace App\Widget\NotiziePrincipali;

use App\Repository\MessaggiRepository;
use Exception;
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

	public function main(string $locale) : Response{
		try{
			$messaggio = $this->messaggiRepository->getUltimoMessaggio($locale);
		}catch(Exception $exception){
			return $this->render('widgets/notizie_principali/notizie_principali_error.html.twig', [
				'messaggio' => $exception->getCode() . " : " . $exception->getMessage(),
			]);
		}

		return $this->render('widgets/notizie_principali/notizie_principali.html.twig', [
			'messaggio' => $messaggio,
		]);
	}
}
