<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\ReteRepository;
use App\Repository\UtentiStrutturaRepository;
use App\ViewModel\AlberoUnilevelVistaViewModel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_reverse;
use function date;
use function dd;
use function trim;

/**
 * Controller della tua rete
 */
class LaTuaReteController extends AbstractController{
	/**
	 * @param ReteRepository            $reteRepository
	 * @param UtentiStrutturaRepository $utentiStrutturaRepository
	 */
	public function __construct(
		private ReteRepository            $reteRepository,
		private UtentiStrutturaRepository $utentiStrutturaRepository
	){
	}

	/**
	 * Mostra la vista dell'albero unilevel, i campi visualizzativi nei filtri vengono innestati da questo controller
	 * Se la chiamata al repository delle viste non va a buon fine, fa il redirect "ingresso"
	 *
	 * @param string  $_locale
	 * @param Request $request
	 *
	 * @return Response
	 */
	#[Route('/{_locale}/albero-unilevel', name: 'albero-unilevel', methods: ['GET', 'POST'])]
	public function alberoUnilevel(string $_locale, Request $request) : Response{
		$idVista = (int) $request->get('idVista', 0);
		if($request->isMethod('GET') && $idVista > 0){
			$this->addFlash('error', 'Vista non accessibile.');
			return $this->redirectToRoute('albero-unilevel');
		}

		try{
			//region mesi
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
			//endregion

			//region livelli
			$livelli = [];
			for($i = 1; $i <= 10; $i++){
				$livelli[] = [
					'label' => $i,
					'value' => $i,
				];
			}
			for($i = 20; $i <= 50; $i = $i + 10){
				$livelli[] = [
					'label' => $i,
					'value' => $i,
				];
			}
			$livelli[] = [
				'label' => 'Tutti',
				'value' => '99999',
			];
			//endregion

			//region altezze & larghezze
			$altezze = [];
			$altezze[] = [
				'label' => 'Auto',
				'value' => 'auto',
			];
			for($i = 600; $i <= 10000; $i += 100){
				$altezze[] = [
					'label' => $i,
					'value' => $i,
				];
			}

			$larghezze = [];
			$larghezze[] = [
				'label' => 'Auto',
				'value' => 'auto',
			];
			for($i = 600; $i <= 10000; $i += 100){
				$larghezze[] = [
					'label' => $i,
					'value' => $i,
				];
			}
			//endregion

			//region elencoViste
			$elencoViste = [];
			$vistaRichiesta = null;
			foreach($this->reteRepository->allVisteUnilevelOfIdUtente($_locale) as $vista){
				if($vista->isDefault()){
					$vista->setId(0);
				}
				$elencoViste[] = $vista;
				if($vista->getId() === $idVista){
					$vistaRichiesta = $vista;
				}
			}
			if($vistaRichiesta === null){
				if($elencoViste[0] instanceof AlberoUnilevelVistaViewModel){
					$vistaRichiesta = $elencoViste[0];
				}else{
					throw new Exception('Non è stato possibile trovare la vistaRichiesta e non è presente neppure quella di Default!');
				}
			}
			//endregion

			return $this->render('pages/la_tua_rete/albero_unilevel.html.twig', [
				'mesi'           => $mesi,
				'livelli'        => $livelli,
				'altezze'        => $altezze,
				'larghezze'      => $larghezze,
				'elencoViste'    => $elencoViste,
				'vistaRichiesta' => $vistaRichiesta,
			]);
		}catch(Exception $exception){
			$this->addFlash('error', 'Errore nel caricamento delle viste: ' . $exception->getMessage());

			return $this->redirectToRoute('ingresso');
		}
	}

	/**
	 * Chiamata api, restituisce il json che crea l'albero
	 *
	 * @param string  $_locale
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	#[Route('/{_locale}/albero-unilevel-ajax', name: 'albero-unilevel-ajax', methods: ['GET'])]
	public function alberoUnilevelAjax(string $_locale, Request $request){
		$idUtente = (int) $request->query->get('idUtente', 0);
		$mese = trim($request->query->get('mese', date('Y-m')));
		$punti = trim($request->query->get('punti', 'pv_mensili'));

		[, $data] = $this->reteRepository->unilevelTreeOfIdUtente($idUtente, $mese, $punti, 'ranks', false, false, $_locale);
		return $this->json($data);
	}

	/**
	 * Salvataggio nuova vista albero
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	#[Route('/{_locale}/salva-albero-unilevel', name: 'salva-albero-unilevel', methods: ['POST'])]
	public function salvaAlberoUnilevel(Request $request){
		//region Preparazione VistaUnilevelTree
		if($request->get('isNew', 0) == 1){
			$ID_vista = 0;
			$isDefault = false;
			$Nome = trim($request->get('nomeNuovaVista', ''));
		}else{
			$ID_vista = (int) $request->get('idVista', 0);
			$isDefault = ($ID_vista == 0);
			$Nome = trim($request->get('nomeVista', ''));
		}
		$ID_utente_albero = (int) $request->get('idUtente', 0);
		$max_livelli = (int) $request->get('livello', 0);
		$Mese = trim($request->get('mese', ''));
		$height = trim($request->get('altezza', ''));
		if($height == 'auto'){
			$height = 0;
		}
		$width = trim($request->get('larghezza', ''));
		if($width == 'auto'){
			$width = 0;
		}
		$orientamento = trim($request->get('orientamento', ''));
		$icon = 'ranks';
		$punti = trim($request->get('punti', ''));
		//endregion
		//dd($isDefault, $ID_vista, $Nome, $ID_utente_albero, $max_livelli, $Mese, (int) $height, (int) $width, $orientamento, $icon, $punti);
		try{
			[$result, $error_msg] = $this->reteRepository->salvaAlberoVista($isDefault, $ID_vista, $Nome, $ID_utente_albero, $max_livelli, $Mese, (int) $height, (int) $width, $orientamento, $icon, $punti);
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
	 * @param Request $request
	 *
	 * @return RedirectResponse
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
	 * @return Response
	 */
	#[Route('/{_locale}/struttura-unilevel/{utenza}/{diretti}', name: 'struttura-unilevel', defaults: ['utenza' => '', 'diretti' => ''], methods: ['GET'])]
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
	 * @param Request $request
	 *
	 * @return JsonResponse
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

		$filtroColonnaOrdinamento = match ($order[0]['column']) {
			1 => 'livello',
			2 => 'incaricato',
			3 => 'qualifica',
			4 => 'email',
			5 => 'cellulare',
			6 => 'sponsor',
			default => 'codice',
		};
		$filtroDirezioneOrdinamento = match ($order[0]['dir']) {
			'desc' => 'desc',
			default => 'asc',
		};

		$filtroDirezioneOrdinamento = strtoupper($filtroDirezioneOrdinamento);

		$strutturaGenerator = $this->utentiStrutturaRepository->getUtentiStruttura($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $tipologiaUtenza, $items, $pag);
		$struttura = [];

		if($strutturaGenerator != null){
			foreach($strutturaGenerator as $item){
				$array = [];

				$array[] = [$item->getCodice(), $item->getLivello(), $item->getNominativo(), "<span class='" . $item->getColore() . "'>" . $item->getQualifica() . "</span > ", $item->getEmail(), $item->getCellulare(), $item->getSponsor()];
				$struttura[] = array_values($array[0]);
			}
		}

		$jsonDatatableStruttura = array(
			'draw'            => time(),
			'recordsTotal'    => count($struttura),
			'recordsFiltered' => count($struttura),
			'data'            => $struttura,
		);

		return $this->json($jsonDatatableStruttura);
	}
}
