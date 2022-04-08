<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\CarrelloRepository;
use App\Repository\FiltriRepository;
use App\Repository\OrdiniRepository;
use App\Repository\StoresRepository;
use App\Repository\UtentiRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller di gestione degli ordini
 */
class OrdiniController extends AbstractController{
	public function __construct(
		private StoresRepository   $storesRepository,
		private UtentiRepository   $utentiRepository,
		private OrdiniRepository   $ordiniRepository,
		private CarrelloRepository $carrelloRepository,
		private FiltriRepository   $filtriRepository,
		private AccountRepository  $accountRepository,
	){
	}

	/**
	 * Vista di scelta dello store al quale un utente può accedere in base alla sua selling zone
	 *
	 */
	#[Route('{_locale}/crea-nuovo-ordine', name: 'crea-nuovo-ordine', methods: ['GET'])]
	public function creaNuovoOrdine(Request $request) : Response{
		//é false quando non viene gestito da noi(.env)
		if($this->getParameter('enable_crea_ordine') === 'false'){
			return $this->redirectToRoute('error404');
		}

		$sellingzoneGenerator = $this->storesRepository->getStore();

		if($sellingzoneGenerator != null){
			$content = $this->json($sellingzoneGenerator)->getContent();
			$sellingZones = json_decode($content, true);

			//region Verifico i tipi di ordine e rimuovo quelli non appartenenti al cliente o all'incaricato a seconda del ruolo dell'utente loggato
			$account = $this->accountRepository->getAccount($this->getUser()->getCodice(), $request->getLocale());
			if($account->getRuolo() === 'Incaricato'){
				foreach($sellingZones as $chiave => $valore){
					$arrayTipiOrdineSellingZone = $valore["tipiOrdine"];
					$i = 0;
					foreach($arrayTipiOrdineSellingZone as $posizione => $items){
						if($items['id'] == 2){
							unset($sellingZones[$chiave]["tipiOrdine"][$i]);
						}
						$i++;
					}
				}
			}
			if($account->getRuolo() === 'Cliente'){
				foreach($sellingZones as $chiave => $valore){
					$arrayTipiOrdineSellingZone = $valore["tipiOrdine"];
					$i = 0;
					foreach($arrayTipiOrdineSellingZone as $posizione => $items){
						if($items['id'] != 2){
							unset($sellingZones[$chiave]["tipiOrdine"][$i]);
						}
						$i++;
					}
				}
			}
			//endregion

			$countSellingZoneS = count($sellingZones);
			$countTipoOrdine = count($sellingZones[0]['tipiOrdine']);

			if($countSellingZoneS > 1 || ($countSellingZoneS === 1 && $countTipoOrdine > 1)){
				return $this->render('pages/ordini/crea_nuovo_ordine.html.twig', ['stores' => $sellingZones]);
			}elseif($countSellingZoneS === 1 && $countTipoOrdine === 1){
				return $this->redirectToRoute('shop');
			}else{
				$this->addFlash('error', 'Errore nel caricamento.');

				return $this->redirectToRoute('ingresso');
			}
		}
		$this->addFlash('error', 'Errore nel caricamento.');

		return $this->redirectToRoute('ingresso');
	}

	/**
	 * Vista di scelta dello store al quale un utente può accedere in base alla sua selling zone
	 */
	#[Route('apri-nuovo-carrello', name: 'apri-nuovo-carrello', methods: ['GET'])]
	public function apriNuovoCarrello(Request $request) : Response{
		$store = trim($request->query->get('store', ''));
		$listino = trim($request->query->get('listino', ''));
		$id_tipo_ordine = trim($request->query->get('id_tipo_ordine', ''));

		try{
			[$result, $error_msg] = $this->carrelloRepository->apriNuovoCarrello($store, $listino, $id_tipo_ordine);
			if($result){
				return $this->redirectToRoute('shop');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('crea-nuovo-ordine');
	}

	/**
	 *
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[
		Route('/ordine-creato-contoTerzi', name: 'ordine-creato-conto-terzi', methods: ['GET', 'POST'])]
	public function ordineCreatoContoTerzi(Request $request){
		$request->request->all();

		//////todo inviare una port a carrello/crea con tipo di ordine
		return $this->redirectToRoute('shop');
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/ordine-creato', name: 'ordine-creato', methods: ['POST'])]
	public function ordineCreato(){
		return $this->redirectToRoute('ingresso');
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	#[Route('/utenti-ajax', name: 'utenti-ajax', methods: ['GET'])]
	public function utentiAjax(Request $request){
		$cerca = trim($request->query->get('query', ''));

		$utenti = $this->utentiRepository->getUtente($cerca);
		if($utenti === null){
			$utenti = [];
		}

		return $this->json($utenti);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	#[Route('/utenti-ordine-gruppo-ajax', name: 'utenti-ordine-gruppo-ajax', methods: ['GET'])]
	public function utentiOrdineGruppo(Request $request){
		$cerca = trim($request->query->get('query', ''));

		$utenti = $this->utentiRepository->getUtente($cerca);
		if($utenti === null){
			$utenti = [];
		}

		return $this->json($utenti);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	#[Route('/utenti-cliente-ajax', name: 'utenti-cliente-ajax', methods: ['GET'])]
	public function utentiOrdineClienteAjax(Request $request){
		$cerca = trim($request->query->get('query', ''));

		$utenti = $this->utentiRepository->getUtente($cerca);
		if($utenti === null){
			$utenti = [];
		}

		return $this->json($utenti);
	}

	/**
	 * Vista elenco ordini
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('{_locale}/elenco-ordini', name: 'elenco-ordini', methods: ['GET'])]
	public function elencOrdini($_locale){
		$ordiniGenerator = $this->ordiniRepository->getOrdini('', '', '', '', '', '', '', '', '', '');

		//if($ordiniGenerator != null && iterator_count($ordiniGenerator) == 0){
		//	return $this->render('pages/ordini/nessun-ordine.html.twig');
		//}

		$filtriEsito = $this->filtriRepository->getFiltriEsito($_locale);
		$filtriTipoOrdine = $this->filtriRepository->getFiltriTipoOrdine($_locale);

		if($filtriEsito == null){
			$filtriEsito = [];
		}

		if($filtriTipoOrdine == null){
			$filtriTipoOrdine = [];
		}

		return $this->render('pages/ordini/elenco_ordini.html.twig', [
			'filtriEsito'      => $filtriEsito,
			'filtriTipoOrdine' => $filtriTipoOrdine,
		]);
	}

	/**
	 * Lavora i dati presi dal repository degli ordini e li mostra nel datatable
	 */
	#[Route('/ordini-ajax', name: 'ordini-ajax', methods: ['GET'])]
	public function ordiniAjax(Request $request) : JsonResponse{
		$order = $request->get('order', [['column' => 0, 'dir' => 'asc']]);
		$sottoposti = $request->query->get('sottoposti', '');
		$clienti = $request->query->get('ricerca_clienti', '');
		$esito = $request->query->get('id_esito', '');
		$data_dal = $request->query->get('data_contratto_inizio', '');
		$data_al = $request->query->get('data_contratto_fine', '');
		$tipolgia_ordine = $request->query->get('tipologia_ordine', '');
		$pag = ($request->get('start', '0'));
		$items = ($request->get('length', '0'));

		switch($order[0]['column']){
			case 0:
				$filtroColonnaOrdinamento = 'data';
				break;
			case 1:
				$filtroColonnaOrdinamento = 'codice';
				break;
			case 2:
				$filtroColonnaOrdinamento = 'incaricato';
				break;
			case 3:
				$filtroColonnaOrdinamento = 'pc';
				break;
			case 4:
				$filtroColonnaOrdinamento = 'totale';
				break;
			case 5:
				$filtroColonnaOrdinamento = 'tipologia_ordine';
				break;
			case 6:
				$filtroColonnaOrdinamento = 'esito';
				break;
			default:
				$filtroColonnaOrdinamento = 'codice';
		}
		switch($order[0]['dir']){
			case 'asc':
				$filtroDirezioneOrdinamento = 'asc';
				break;
			case 'desc':
				$filtroDirezioneOrdinamento = 'desc';
				break;
			default:
				$filtroDirezioneOrdinamento = 'asc';
		}

		$filtroDirezioneOrdinamento = strtoupper($filtroDirezioneOrdinamento);
		//dd($sottoposti, $clienti, $esito, $data_dal, $data_al, $tipolgia_ordine, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $pag, $items);
		$ordini = $this->ordiniRepository->getOrdini($sottoposti, $clienti, $esito, $data_dal, $data_al, $tipolgia_ordine, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $items, $pag);
		$datatableorders = [];
		//region se i dati presi dal repository degli ordini sono validi, li dispongo per visualizzarli nel datatable
		if($ordini != null){
			foreach($ordini as $item){
				$ordine = [];
				$data = $item->getData();
				$ordine[] = substr($data, 8, 2) . "-" . substr($data, 5, 2) . "-" . substr($data, 0, 4);
				//$ordine[] = $item->getData();
				$ordine[] = $item->getCodice();
				$ordine[] = $item->getUser();
				$ordine[] = $item->getPc();
				$ordine[] = $item->getTotale();
				$ordine[] = $item->getTipologiaOrdine();
				$ordine[] = '<span style="color:' . $item->getEsitoColore() . ';">' . $item->getEsito() . '</span>';
				//if($item->isVisibile() == '1'){
				//	$ordine[] = '<a href="dettaglio-ordine/' . $item->getId() . '"><button type="button" class="display-inherit btn btn-square btn-outline-light btn-sm text-dark">DETTAGLI <i class="icon-zoom-in"></i></button></a>';
				//}else{
				//	$ordine[] = "";
				//}
				$datatableorders[] = $ordine;
			}
		}
		//endregion

		$elencoOrdini = array(
			'draw'            => time(),
			'recordsTotal'    => count($datatableorders),
			'recordsFiltered' => count($datatableorders),
			'data'            => $datatableorders,
		);

		return $this->json($elencoOrdini);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}dettaglio-ordine/{id}', name: 'dettaglio-ordine', methods: ['GET'])]
	public function dettaglioOrdine(int $id){
		return $this->render('pages/ordini/dettaglio_ordine.html.twig', [
			'id' => $id,
		]);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/ordine-concluso/{id}', name: 'ordine-concluso', methods: ['GET'])]
	public function ordineConcluso(int $id){
		return $this->render('pages/ordini/ordine-concluso.html.twig',
			[
				'modalitaPagamento' => $this->ordiniRepository->getDettaglioOrdine($id),
			]);
	}
}