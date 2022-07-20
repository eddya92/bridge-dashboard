<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\EmailsRepository;
use App\Repository\SitoPersonaleRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller di gestione dei marketing tools
 */
class MarketingToolsController extends AbstractController{
	/**
	 * @param \App\Repository\EmailsRepository        $emailRepository
	 * @param \App\Repository\SitoPersonaleRepository $sitoPersonaleRepository
	 */
	public function __construct(
		private EmailsRepository        $emailRepository,
		private SitoPersonaleRepository $sitoPersonaleRepository,
	){
	}

	/**
	 * Restituisce la vista della scelta del template per inviare una mail
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/invita-persone', name: 'invita-persone', methods: ['GET'])]
	public function invitaPersone(string $_locale){
		try{
			$templatesGenerator = $this->emailRepository->getEmailTemplates($_locale);
		}catch(Exception $exception){
			$this->addFlash('error', $exception->getCode() . " : " . $exception->getMessage());

			return $this->redirectToRoute('ingresso');
		}

		$templates = [];
		$email = [];

		foreach($templatesGenerator as $i => $template){
			/** @var $template \App\ViewModel\EmailTemplateViewModel */
			if($i == 0){
				$email['email_1h'] = $template->getEmail1h();
				$email['email_1d'] = $template->getEmail1d();
				$email['email_1w'] = $template->getEmail1w();
				$email['email_1m'] = $template->getEmail1m();
				$email['email_1h_totali'] = $template->getEmail1hTotali();
				$email['email_1d_totali'] = $template->getEmail1dTotali();
				$email['email_1w_totali'] = $template->getEmail1wTotali();
				$email['email_1m_totali'] = $template->getEmail1mTotali();
			}
			$templates[] = $template;
		}

		$elencoLingueDisponibili = ['it','en'];

		return $this->render('pages/marketing_tools/invita_persone.html.twig', [
			'email'                   => $email,
			'templates'               => $templates,
			'elencoLingueDisponibili' => $elencoLingueDisponibili,

		]);
	}

	/**
	 * Invia invito tramite amil
	 * se la chiamata fallisce, redirect nella pagina della scelta template "invita-persone"
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/invio-invito', name: 'invia-invito', methods: ['POST'])]
	public function inviaInvito(Request $request){
		$nome = trim($request->get('nome'));
		$email = trim($request->get('email'));
		$idEmail = (int) trim($request->get('id_email'));

		try{
			[$result, $error_msg] = $this->emailRepository->inviaInvito($nome, $email, $idEmail);
			if($result){
				$this->addFlash('success', 'email inviata correttamente');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('invita-persone');
	}

	/**
	 *
	 * Chiamata api, torna il json che popola datatable delle email inviate
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 * @throws \Exception
	 */
	#[Route('/email-ajax', name: 'email-ajax', methods: ['GET'])]
	public function emailAjax() : JsonResponse{
		try{
			$emailsGenerator = $this->emailRepository->getElencoEmailInviate();
			$emailsInviate = [];

			/** @var $email \App\ViewModel\EmailViewModel */
			foreach($emailsGenerator as $email){
				$item = [];
				$item[] = '<span>' . (new DateTime($email->getData()))->format('d/m/Y') . '</span>';
				$item[] = '<span>' . $email->getUser() . '</span>';
				$item[] = '<span>' . $email->getEmail() . '</span>';
				$item[] = '<a href=""><button type="button" class="btn btn-light color-black">Invia di nuovo</button></a>';
				$emailsInviate[] = $item;
			}

			$count = count($emailsInviate);

			return $this->json([
				'draw'            => time(),
				'recordsTotal'    => $count,
				'recordsFiltered' => $count,
				'data'            => $emailsInviate,
			]);
		}catch(Exception $exception){
			return $this->json([
				'draw'            => time(),
				'recordsTotal'    => 0,
				'recordsFiltered' => 0,
				'data'            => [],
			]);
		}
	}

	/**
	 * @return Response
	 */
	#[Route('/{_locale}/sito-personale', name: 'sito-personale', methods: ['GET'])]
	public function sitoPersonaleView(string $_locale){
		$sitoPersonale = $this->sitoPersonaleRepository->getSitoPersonale($_locale);

		if($sitoPersonale != null){
			return $this->render('pages/marketing_tools/sito_personale.html.twig',
				[
					'sito' => $sitoPersonale,
				]);
		}else{
			$this->addFlash('error', 'errore caricamento pagina');

			return $this->redirectToRoute('ingresso');
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/aggiorna-sito', name: 'aggiorna-sito', methods: ['POST', 'GET'])]
	public function aggiornaSito(Request $request){
		$titolo = $request->request->get('titolo', '');
		$descrizione = $request->request->get('descrizione', '');
		$telefono = $request->request->get('telefono', '');
		$email = $request->request->get('email', '');
		$facebook = $request->request->get('facebook', '');
		$instagram = $request->request->get('instagram', '');
		$twitter = $request->request->get('twitter', '');
		$youtube = $request->request->get('youtube', '');
		$immagine = ['name' => '', 'content' => ''];
		if(array_key_exists('immagine', $_FILES) && $_FILES['immagine']['error'] == 0){
			$immagine = file_get_contents($_FILES['immagine']['tmp_name']);

			$immagine = ['name' => $_FILES['immagine']['name'], 'content' => base64_encode($immagine)];
			unlink($_FILES['immagine']['tmp_name']);
		}
		//$intestazione = $_FILES['intestazione'];

		try{
			[$result, $error_msg] = $this->sitoPersonaleRepository->aggiornaSitoPersonale($titolo, $descrizione, $telefono, $email, $facebook, $instagram, $twitter, $youtube, $immagine);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('sito-personale',
		);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/website/{uri}', name: 'minisito', methods: ['GET'])]
	public function minisito(string $_locale, string $uri){
		$sitoPersonale = $this->sitoPersonaleRepository->getMinisito($_locale, $uri);

		if($sitoPersonale != null){
			return $this->render('pages/marketing_tools/minisito.html.twig',
				[
					'sito' => $sitoPersonale,
				]);
		}else{
			$this->addFlash('error', 'errore caricamento pagina');

			return $this->redirectToRoute('ingresso');
		}
	}
}