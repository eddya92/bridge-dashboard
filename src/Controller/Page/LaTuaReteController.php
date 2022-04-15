<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\ReteRepository;
use App\Repository\UtentiStrutturaRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller della tua rete
 */
class LaTuaReteController extends AbstractController{
	/**
	 * @param \App\Repository\ReteRepository            $reteRepository
	 * @param \App\Repository\UtentiStrutturaRepository $utentiStrutturaRepository
	 */
	public function __construct(
		private ReteRepository            $reteRepository,
		private UtentiStrutturaRepository $utentiStrutturaRepository
	){
	}

	/**
	 * Mostra la vista dell'albero unilevel, i campi visualizzativi nei filtri vengono innestati da questo controller
	 * Se il la chiamata al repository delle viste non va a buon fine, fa il redirect "ingresso"
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/albero-unilevel', name: 'albero-unilevel', methods: ['GET', 'POST'])]
	public function alberoUnilevel(string $_locale, Request $request) : Response{
		$id = (int) $request->get('idVista', 0);

		if($request->isMethod('GET')){
			if($id != 0){
				$this->addFlash('error', 'Vista non accessibile.');

				return $this->redirectToRoute('albero-unilevel');
			}
		}

		$viste = [];
		$vista_richiesta = null;
		$vista_generator = $this->reteRepository->getAlberoViste($_locale);
		$count = 0;
		if($vista_generator != null){
			foreach($vista_generator as $vista){
				$viste[] = $vista;
				if($id > 0){
					if($vista->getId() == $id){
						$vista_richiesta = $vista;
					}
				}else{
					if($count == 0){
						$vista_richiesta = $vista;
					}
					if($vista->isPrincipale()){
						$vista_richiesta = $vista;
					}
				}
			}
			if($request->isMethod('POST') && $vista_richiesta == null){
				$this->addFlash('error', 'Vista non accessibile.');

				return $this->redirectToRoute('albero-unilevel');
			}

			$mesi = [];
			for($anno = 2021; $anno <= date('Y'); $anno++){
				if($anno == date('Y')){
					for($mese = 1; $mese <= date('m'); $mese++){
						$m = ($mese < 10 ? '0' : '') . $mese;

						$mesi[] = [
							'label' => $m . '/' . $anno,
							'value' => $anno . '-' . $m,
						];
					}
				}else{
					for($mese = 1; $mese <= 12; $mese++){
						$m = ($mese < 10 ? '0' : '') . $mese;

						$mesi[] = [
							'label' => $m . '/' . $anno,
							'value' => $anno . '-' . $m,
						];
					}
				}
			}

			$mesi = array_reverse($mesi);

			$livelli = [];
			$livelli[] = [
				'label' => 'Tutti',
				'value' => '0',
			];
			$livelli[] = [
				'label' => '1',
				'value' => '1',
			];
			$livelli[] = [
				'label' => '2',
				'value' => '2',
			];
			$livelli[] = [
				'label' => '5',
				'value' => '5',
			];
			$livelli[] = [
				'label' => '10',
				'value' => '10',
			];
			$livelli[] = [
				'label' => '50',
				'value' => '50',
			];

			$altezze = [];
			$altezze[] = [
				'label' => 'Auto',
				'value' => 'auto',
			];
			$altezze[] = [
				'label' => '900',
				'value' => '900',
			];
			$altezze[] = [
				'label' => '1000',
				'value' => '1000',
			];
			$altezze[] = [
				'label' => '1100',
				'value' => '1100',
			];
			$altezze[] = [
				'label' => '1200',
				'value' => '1200',
			];
			$altezze[] = [
				'label' => '4000',
				'value' => '4000',
			];

			$larghezze = [];
			$larghezze[] = [
				'label' => 'Auto',
				'value' => 'auto',
			];
			$larghezze[] = [
				'label' => '900',
				'value' => '900',
			];
			$larghezze[] = [
				'label' => '1000',
				'value' => '1000',
			];
			$larghezze[] = [
				'label' => '1100',
				'value' => '1100',
			];
			$larghezze[] = [
				'label' => '1200',
				'value' => '1200',
			];
			$larghezze[] = [
				'label' => '4000',
				'value' => '4000',
			];

			return $this->render('pages/la_tua_rete/albero_unilevel.html.twig', [
				'mesi'      => $mesi,
				'livelli'   => $livelli,
				'altezze'   => $altezze,
				'larghezze' => $larghezze,
				'viste'     => $viste,
				'vista'     => $vista_richiesta,
			]);
		}else{
			$this->addFlash('error', 'Errore nel caricamento delle viste.');

			return $this->redirectToRoute('ingresso');
		}
	}

	/**
	 * Chiamata api, restituisce il json che crea l'albero
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	#[Route('/{_locale}/albero-unilevel-ajax', name: 'albero-unilevel-ajax', methods: ['GET'])]
	public function alberoUnilevelAjax(string $_locale, Request $request){
		$idUtente = (int) $request->query->get('idUtente', 0);
		$idVista = (int) $request->query->get('idVista', 0);
		$livello = (int) $request->query->get('livello', 0);
		$mese = trim($request->query->get('mese', date('Y-m')));
		$punti = trim($request->query->get('punti', 'pv_mensili'));

		[$result, $data] = $this->reteRepository->getAlberoUnilevel($idUtente, $livello, $mese, $punti, $idVista,$_locale);
		if($result){
			return $this->json($data);
		}else{
			return $this->json($data);
		}
	}

	/**
	 * Salvataggio nuova vista albero
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/{_locale}/salva-albero-unilevel', name: 'salva-albero-unilevel', methods: ['POST'])]
	public function salvaAlberoUnilevel(Request $request){
		$idVista = (int) $request->get('idVista', 0);
		$idUtente = (int) $request->get('idUtente', 0);
		$livello = (int) $request->get('livello', 0);
		$nome = trim($request->get('nome', ''));
		$mese = trim($request->get('mese', ''));
		$altezza = trim($request->get('altezza', ''));
		$larghezza = trim($request->get('larghezza', ''));
		$orientamento = trim($request->get('orientamento', ''));
		$punti = trim($request->get('punti', ''));

		try{
			[$result, $error_msg] = $this->reteRepository->salvaAlberoVista(0, $nome, $idUtente, $livello, $mese, $altezza, $larghezza, $orientamento, $punti, $idVista);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('albero-unilevel');
	}

	/**
	 * Elimina una vista Albero
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/{_locale}/elimina-albero-unilevel', name: 'elimina-albero-unilevel', methods: ['POST'])]
	public function eliminaAlberoUnilevel(Request $request){
		$id = (int) $request->get('idVistaElimina', 0);

		try{
			[$result, $error_msg] = $this->reteRepository->eliminaAlberoVista($id);
			if($result){
				$this->addFlash('success', 'Vista eliminata correttamente');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('albero-unilevel');
	}

	/**
	 * Vista della tua struttura
	 * se la rotta viene chiamata con il parametro clienti es:
	 * struttura-unilevel/clienti ->filtri preimpostati su tipologia utenza = clienti
	 *
	 * se la rotta viene chiamata con il parametro incaricati es:
	 * struttura-unilevel/incaricati ->filtri preimpostati su tipologia utenza = incaricati
	 *
	 * per ricevere solo i diretti fare
	 * struttura-unilevel/diretti
	 *
	 * per ricevere solo i diretti dei clienti/incaricati
	 * struttura-unilevel/incaricati/diretti
	 *
	 * se la rotta vieene chiamata struttura-unilevel/clienti ->filtri preimpostati su tipologia utenza = clienti
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/struttura-unilevel/{utenza}/{diretti}', name: 'struttura-unilevel', methods: ['GET'], defaults: ['utenza' => '', 'diretti' => ''])]
	public function strutturaUnilevel(Request $request){
		$tipologiaUtenza = $request->get('utenza', '');
		$diretti = $request->get('diretti', '');

		if($diretti != 'diretti'){
			$diretti = '';
		}

		if($tipologiaUtenza === 'diretti'){
			$diretti = 'diretti';
		}

		return $this->render('pages/la_tua_rete/la_tua_struttura.html.twig', [
			'tipologiaUtenza' => $tipologiaUtenza,
			'diretti'         => $diretti,
		]);
	}

	/**
	 * Chiamata api, restituisce il json che popola il datatable della tua struttura
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	#[Route('/{_locale}/struttura-ajax', name: 'struttura-personale-ajax', methods: ['GET'])]
	public function strutturaPersonaleAjax(Request $request){
		$order = $request->get('order', [['column' => 0, 'dir' => 'asc']]);
		$tipologiaUtenza = $request->get('filtro_tipoligiaUtente', '');
		$filtroGruppoDi = $request->get('filtro_gruppoDi', '');
		$filtroNominativo = $request->get('filtro_nominativo', '');
		$filtroEmail = trim($request->get('filtro_email', ''));
		$filtroCellulare = trim($request->get('filtro_cellulare', ''));
		$filtroPeriodo = trim($request->get('filtro_periodo', ''));
		$filtroDiretti = ($request->get('filtro_diretti', 'false'));
		$pag = ($request->get('start', '0'));
		$items = ($request->get('length', '0'));

		switch($order[0]['column']){
			case 0:
				$filtroColonnaOrdinamento = 'codice';
				break;
			case 1:
				$filtroColonnaOrdinamento = 'livello';
				break;
			case 2:
				$filtroColonnaOrdinamento = 'incaricato';
				break;
			case 3:
				$filtroColonnaOrdinamento = 'qualifica';
				break;
			case 4:
				$filtroColonnaOrdinamento = 'email';
				break;
			case 5:
				$filtroColonnaOrdinamento = 'cellulare';
				break;
			case 6:
				$filtroColonnaOrdinamento = 'sponsor';
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

		//dd($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $tipologiaUtenza, $items,$pag);
		$strutturaGenerator = $this->utentiStrutturaRepository->getUtentiStruttura($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $tipologiaUtenza, $items, $pag);
		$struttura = [];

		if($strutturaGenerator != null){
			foreach($strutturaGenerator as $item){
				$array = [];

				$array[] = [$item->getCodice(), $item->getLivello(), $item->getNominativo(), "<span class='" . $item->getColore() . "'>" . $item->getQualifica() . "</span > ", $item->getEmail(), $item->getCellulare(), $item->getSponsor()];
				//array_push($array, $item->getCodice(), $item->getLivello(), $item->getNominativo(), "<span class='" . $item->getColore() . "'>" . $item->getQualifica() . "</span > ", $item->getEmail(), $item->getCellulare(), $item->getSponsor());
				$struttura[] = array_values($array[0]);
			}
		};

		$jsonDatatableStruttura = array(
			'draw'            => time(),
			'recordsTotal'    => count($struttura),
			'recordsFiltered' => count($struttura),
			'data'            => $struttura,
		);

		return $this->json($jsonDatatableStruttura);
	}

	//	public function strutturaPersonaleAjaxs(Request $request){
	//		$order = $request->get('order', [['column' => 0, 'dir' => 'asc']]);
	//
	//		$filtroGruppoDi = $request->get('filtro_gruppoDi', '');
	//		$filtroNominativo = $request->get('filtro_nominativo', '');
	//		$filtroEmail = trim($request->get('filtro_email', ''));
	//		$filtroCellulare = trim($request->get('filtro_cellulare', ''));
	//		$filtroPeriodo = trim($request->get('filtro_periodo', ''));
	//		$filtroDiretti = ($request->get('filtro_diretti', 'false'));
	//
	//		switch($order[0]['column']){
	//			case 0:
	//				$filtroColonnaOrdinamento = 'codice';
	//				break;
	//			case 1:
	//				$filtroColonnaOrdinamento = 'livello';
	//				break;
	//			case 2:
	//				$filtroColonnaOrdinamento = 'incaricato';
	//				break;
	//			case 3:
	//				$filtroColonnaOrdinamento = 'qualifica';
	//				break;
	//			case 4:
	//				$filtroColonnaOrdinamento = 'email';
	//				break;
	//			case 5:
	//				$filtroColonnaOrdinamento = 'cellulare';
	//				break;
	//			case 6:
	//				$filtroColonnaOrdinamento = 'sponsor';
	//				break;
	//			default:
	//				$filtroColonnaOrdinamento = 'codice';
	//		}
	//
	//		switch($order[0]['dir']){
	//			case 'asc':
	//				$filtroDirezioneOrdinamento = 'asc';
	//				break;
	//			case 'desc':
	//				$filtroDirezioneOrdinamento = 'desc';
	//				break;
	//			default:
	//				$filtroDirezioneOrdinamento = 'asc';
	//		}
	//
	//		$filtroColonnaOrdinamento = strtoupper($filtroColonnaOrdinamento);
	//		$filtroDirezioneOrdinamento = strtoupper($filtroDirezioneOrdinamento);
	//
	//		$items = $request->get('items', 10);
	//		$tipologiaUtenza = $request->get('filtro_tipoligiaUtente', '');
	//		//dd($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $items, $tipologiaUtenza);
	//		$strutturaGenerator = $this->utentiStrutturaRepository->getUtentiStruttura($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $items, $tipologiaUtenza);
	//		$struttura = [];
	//
	//		if($strutturaGenerator != null){
	//			foreach($strutturaGenerator as $item){
	//				$array = [];
	//
	//				$array[] = [$item->getCodice(), $item->getLivello(), $item->getNominativo(), "<span class='" . $item->getColore() . "'>" . $item->getQualifica() . "</span > ", $item->getEmail(), $item->getCellulare(), $item->getSponsor()];
	//				//array_push($array, $item->getCodice(), $item->getLivello(), $item->getNominativo(), "<span class='" . $item->getColore() . "'>" . $item->getQualifica() . "</span > ", $item->getEmail(), $item->getCellulare(), $item->getSponsor());
	//				$struttura[] = array_values($array[0]);
	//			}
	//		}
	//
	//		for($i = 1; $i <= $items; $i++){
	//			$struttura[] = [
	//				'00000' . $i,
	//				'' . $i,
	//				'Nome' . $i,
	//				'Qualifica' . $i + 10,
	//				'Email' . $i,
	//				'3330000000' . $i,
	//				'Nome' . $i + 20,
	//			];
	//		}
	//
	//		usort($struttura, function($a, $b) use ($order){
	//			if($order[0]['dir'] == 'asc'){
	//				return strcmp($a[$order[0]['column']], $b[$order[0]['column']]);
	//			}else{
	//				return strcmp($b[$order[0]['column']], $a[$order[0]['column']]);
	//			}
	//		});
	//
	//		$countStruttura = count($struttura);
	//		$countStrutturaTotali = 1000;
	//
	//		$jsonDatatableStruttura = array(
	//			'draw'            => time(),
	//			'recordsTotal'    => $countStruttura,
	//			'recordsFiltered' => $countStrutturaTotali,
	//			'data'            => $struttura,
	//		);
	//
	//		return $this->json($jsonDatatableStruttura);
	//	}
}