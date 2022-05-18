<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\MessaggiRepository;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * Classe relativa ai messaggi
 *
 */
class MessaggiController extends AbstractController{
	/**
	 * @param \App\Repository\MessaggiRepository $messaggiRepository
	 */
	public function __construct(
		private MessaggiRepository $messaggiRepository,
	){
	}

	/**
	 * Vista relativa ai messaggi.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/messaggi', name: 'messaggi', methods: ['GET'])]
	public function messaggi($_locale){
		try{
			$messaggiGenerator = $this->messaggiRepository->getMessaggi($_locale);
		}catch(Exception $exception){
			$this->addFlash('error',$exception->getCode() . " : " . $exception->getMessage());

			return $this->redirectToRoute('ingresso');
		}

		$messaggi = [];
		$ultimoMessaggioArrivato = [];
		$penultimoMessaggioArrivato = [];
		$terzultimoMessaggioArrivato = [];

		if($messaggiGenerator != null){
			foreach($messaggiGenerator as $messaggioGenerator){
				$messaggi[] = $messaggioGenerator;
			}
			rsort($messaggi);
			$ultimoMessaggioArrivato = $messaggi[0] ?? '';
			$penultimoMessaggioArrivato = $messaggi[1] ?? '';
			$terzultimoMessaggioArrivato = $messaggi[2] ?? '';

			array_shift($messaggi);
			array_shift($messaggi);
			array_shift($messaggi);
		}

		return $this->render('pages/messaggi/messaggi.html.twig', [
			'messaggi'            => $messaggi,
			'ultimoMessaggio'     => $ultimoMessaggioArrivato,
			'penultimoMessaggio'  => $penultimoMessaggioArrivato,
			'terzultimoMessaggio' => $terzultimoMessaggioArrivato,
		]);
	}

	/**
	 * Vista relativo al dettaglio del messaggio
	 *
	 * @param int $id
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/messaggio/{id}', name: 'messaggio', methods: ['GET'])]
	public function messaggioView(int $id, string $_locale){
		try{
			$messaggio = $this->messaggiRepository->getMessaggio($id, $_locale);
		}catch(Exception $exception){
			$this->addFlash('error',$exception->getCode() . " : " . $exception->getMessage());

			return $this->redirectToRoute('messaggi');
		}

		$ID_messaggio_precedente = 0;
		$ID_messaggio_successivo = 0;

		if($messaggio != null){
			$messaggiGenerator = $this->messaggiRepository->getMessaggi($_locale);
			$messaggi = [];
			if($messaggiGenerator != null){
				foreach($messaggiGenerator as $messaggioGenerator){
					$messaggi[] = $messaggioGenerator;
				}
				rsort($messaggi);

				if(!empty($messaggi)){
					$numero_messaggi = count($messaggi);
					for($i = 0; $i < $numero_messaggi; $i++){
						if($messaggi[$i]->getId() == $id){
							$ID_messaggio_precedente = $messaggi[$i > 0 ? $i - 1 : 0]->getId();
							if(($i + 1) == $numero_messaggi){
								$ID_messaggio_successivo = 0;
							}else{
								$ID_messaggio_successivo = $messaggi[$i + 1]->getId();
							}
						}
					}
				}
			}

			return $this->render('pages/messaggi/messaggio.html.twig', [
				'messaggio'             => $messaggio,
				'idMessaggioSuccessivo' => $ID_messaggio_successivo,
				'idMessaggioPrecedente' => $ID_messaggio_precedente,
			]);
		}else{
			$this->addFlash('error', 'Errore durante il caricamento del messaggio');

			return $this->redirectToRoute('messaggi');
		}
	}
}