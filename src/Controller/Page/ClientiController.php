<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\ClientiRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller per la gestione dei clienti
 */
class ClientiController extends AbstractController{
	public function __construct(
		private ClientiRepository $repository,

		private AccountRepository $accountRepository
	){
	}

	/**
	 * Visualizzazione del form per l'inserimento di un nuovo cliente
	 */
	#[Route('/{_locale}/nuovo-cliente', name: 'nuovo-cliente', methods: ['GET'])]
	public function nuovoCliente() : Response{

		//la registrazione del nuovo utente non la gestiamo noi se è false dall'env
		if($this->getParameter('enable_inserisci_cliente') === 'false'){
			return $this->redirectToRoute('error404');
		}
		return $this->render('pages/clienti/inserisci_nuovo_cliente.html.twig');
	}

	/**
	 * Invio dati del form in Post
	 */
	#[Route('/{_locale}/esegui-registrazione-cliente', name: 'esegui-registrazione-cliente', methods: ['GET','POST'])]
	public function eseguiRegistrazioneCliente(Request $request){

		//la registrazione del nuovo utente non la gestiamo noi se è false dall'env
		if($this->getParameter('enable_inserisci_cliente') === 'false'){
			return $this->redirectToRoute('error404');
		}

		$codiceSponsor = $this->getUser()->getCodice();
		$naturaGiuridica = trim($request->request->get('naturaGiuridica', ''));
		$ragioneSociale = trim($request->request->get('ragioneSociale', 'privato'));
		$nome = trim($request->request->get('nome', ''));
		$cognome = trim($request->request->get('cognome', ''));
		$email = trim($request->request->get('email', ''));
		$password = rand(10000000, 100000000) . chr(rand(65, 90));
		$nazione = trim($request->request->get('ID_nazione', ''));

		$agreements = array_map(function($k){
			return str_replace('input_agree_', '', $k);
		}, array_keys(array_filter($request->request->all(), function($v, $k){
			return str_contains($k, 'input_agree_');
		}, ARRAY_FILTER_USE_BOTH)));

		try{
			[$result, $error_msg] = $this->accountRepository->registrazioneCliente($codiceSponsor, $nome, $cognome, $ragioneSociale, $naturaGiuridica, $email, $password, $nazione, $agreements);
			if($result){
				$this->addFlash('success', 'Registrazione avvenuta con successo.');

				return $this->redirectToRoute('login');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('nuovo-cliente');
	}

	/**
	 * Vista della pagina elenco clienti
	 */
	#[Route('/{_locale}/elenco-clienti', name: 'elenco-clienti', methods: ['GET'])]
	public function elencoClienti() : Response{
		return $this->render('pages/clienti/elenco_clienti.html.twig');
	}

	/**
	 * Datatable elenco clienti
	 */
	#[Route('/{_locale}/clienti-ajax', name: 'clienti-ajax', methods: ['GET'])]
	public function clientiAjax(Request $request, $_locale) : JsonResponse{
		$ricercaGenerica = $request->get('ricerca_generica', '');
		$dataDal = $request->get('data_dal', '');
		$dataAl = $request->get('data_al', '');

		$clienti = $this->repository->getClienti($_locale,$ricercaGenerica, $dataDal, $dataAl);

		if($clienti != null){
			$datatableClienti = [];
			foreach($clienti as $item){
				$array = [];
				array_push($array, $item->getDataIscrizione(), $item->getCodice(), $item->getNominativo(), $item->getPc(), $item->getTelefono(), $item->getEmail(), $item->getOrdini());
				$datatableClienti[] = $array;
			}
			$count = count($datatableClienti);
		}else{
			$count = 0;
			$datatableClienti = [];
		}

		$elencoClienti = array(
			'draw'            => 1,
			'recordsTotal'    => $count,
			'recordsFiltered' => $count,
			'data'            => $datatableClienti,
		);

		return $this->json($elencoClienti);
	}
}