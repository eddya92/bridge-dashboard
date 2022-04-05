<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\DocumentiPersonaliRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentiPersonaliController extends AbstractController{
	/**
	 * @param \App\Repository\DocumentiPersonaliRepository $repository
	 */
	public function __construct(
		private DocumentiPersonaliRepository $repository
	){
	}

	/**
	 * Pagina di visualizzazione dei documenti personali
	 */
	#[Route('/{_locale}/documenti-personali', name: 'documenti-personali', methods: ['GET'])]
	public function documentiPersonali(string $_locale) : Response{
		$countObbligatori = 0;
		$documentiObbligatoriCaricati = 0;
		$documentiObbligatori = [];
		$documentiFacoltativi = [];
		$documenti = [];
		
		try{
			$documentiGenerator = $this->repository->getDocumentiPersonali($_locale);
			foreach($documentiGenerator as $documento){
				$documenti[] = $documento;
				if($documento->isObbligatorio()){
					if($documento->isCaricato()){
						$documentiObbligatoriCaricati++;
					}
					$documentiObbligatori[] = $documento;
					$countObbligatori++;
				}else{
					$documentiFacoltativi[] = $documento;
				}
			}
		}catch(\Throwable $exception){
			error_log($exception->getMessage());
			$this->addFlash('error', 'non siamo riusciti a scaricare i documenti,ripovare piu tardi');
			$countObbligatori = 0;
			$documentiObbligatoriCaricati = 0;
			$documentiObbligatori = [];
			$documentiFacoltativi = [];
			$documenti = [];
		}

		return $this->render(
			'pages/profilo/documenti_personali.html.twig', [
				'documenti'                    => $documenti,
				'count'                        => $countObbligatori,
				'documentiObbligatori'         => $documentiObbligatori,
				'documentiObbligatoriCaricati' => $documentiObbligatoriCaricati,
				'documentiFacoltativi'         => $documentiFacoltativi,
			]
		);
	}

	/**
	 * Carica un documento e se tutto va bene fa il redirect con un messaggio success
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/carica-documento-personale', name: 'carica-documento-personale', methods: ['POST'])]
	public function caricaDocumentoPersonale(Request $request) : Response{
		$iddoc = trim($request->get('iddoc', ''));
		$base64doc = trim($request->get('base64doc', ''));
		$namedoc = trim($request->get('namedoc', ''));

		try{
			[$result, $error_msg] = $this->repository->caricaDocumentoPersonale($iddoc, $base64doc, $namedoc);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('documenti-personali');
	}

	/**
	 * Crea il tesserino e se tutto va bene fa il redirect con un messaggio success
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/crea-tesserino', name: 'crea-tesserino', methods: ['GET'])]
	public function creaTesserino(Request $request) : Response{
		try{
			[$result, $error_msg] = $this->repository->creaTesserino();
			if($result){
				$this->addFlash('success', 'Il tuo tesserino è stato aggiornato correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('documenti-personali');
	}

}