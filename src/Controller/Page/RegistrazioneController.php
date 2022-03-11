<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\NazioniRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gestione della pagina di registrazione
 */
final class RegistrazioneController extends AbstractController{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private AccountRepository $accountRepository,
		private NazioniRepository $nazioniRepository
	){
	}

	/**
	 * Vista del form di registrazione
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/registrazione', name: 'registrazione', methods: ['GET'])]
	public function registrazione() : Response{
		//é false quando non viene gestito da noi(.env)
		if($this->getParameter('enable_url_registrazione') === 'false'){
			return $this->redirectToRoute('error404');
		}

		$nazioni = $this->nazioniRepository->getNazioni();

		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		if($nazioni == null){
			$nazioni = [];
		}

		$old_post = $this->get('session')->getFlashBag()->get('old_post', []);
		if(count($old_post) > 0){
			$old_post = $old_post[0];
		}

		return $this->render('pages/registrazione/registrazione.html.twig', [
			'old_post' => $old_post,
			'nazioni'  => $nazioni,
		]);
	}

	/**
	 * Submit del form di registrazione
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/esegui-registrazione', name: 'esegui-registrazione', methods: ['POST'])]
	public function eseguiRegistrazione(Request $request) : Response{
		//é false quando non viene gestito da noi(.env)
		if($this->getParameter('enable_url_registrazione') === 'false'){
			return $this->redirectToRoute('error404');
		}

		$codiceSponsor = trim($request->request->get('codiceSponsor', ''));
		$nome = trim($request->request->get('nome', ''));
		$cognome = trim($request->request->get('cognome', ''));
		$naturaGiuridica = trim($request->request->get('naturaGiuridica', 'privato'));
		$ragioneSociale = trim($request->request->get('ragioneSociale', ''));
		$email = trim($request->request->get('email', ''));
		$password = trim($request->request->get('password', ''));
		$nazione = trim($request->request->get('ID_nazione', ''));

		$agreements = array_map(function($k){
			return str_replace('input_agree_', '', $k);
		}, array_keys(array_filter($request->request->all(), function($v, $k){
			return str_contains($k, 'input_agree_');
		}, ARRAY_FILTER_USE_BOTH)));

		try{
			[$result, $error_msg] = $this->accountRepository->registrazioneCliente($codiceSponsor, $nome, $cognome, $ragioneSociale, $naturaGiuridica, $email, $password, $nazione, $agreements);
			if($result){
				$this->addFlash('success', 'Benvenuto ' . $nome . '.');

				return $this->redirectToRoute('registrazione-success');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('registrazione');
	}

	/**
	 * Vista della pagina di avvenuta registrazione
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/registrazione-success', name: 'registrazione-success', methods: ['GET'])]
	public function registrazione_success() : Response{
		//é false quando non viene gestito da noi(.env)
		if($this->getParameter('enable_url_registrazione') === 'false'){
			return $this->redirectToRoute('error404');
		}

		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		return $this->render('pages/registrazione/registrazione_success.html.twig', [
		]);
	}
}
