<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\BonusRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function dd;

/**
 * Controller per la pagina dei BONUS
 */
class BonusController extends AbstractController{
	/**
	 * @param BonusRepository $repository
	 */
	public function __construct(
		private BonusRepository   $repository,
		private AccountRepository $accountRepository,
	){
	}

	/**
	 * Mostra i dati relativi ai guadagni dell'anno scelto, se l'anno non è stato selezionato di defaul passiamo l'anno corrente
	 *
	 * @param string $_locale
	 * @param int    $anno
	 *
	 * @return Response
	 */
	#[Route('/{_locale}/bonus/{anno}', name: 'bonus', methods: ['GET'])]
	public function bonusView(string $_locale, int $anno = 0) : Response{
		//region Creo gli anni da $dataInizio ad $annoAttuale
		$annoAttuale = date('Y');
		$account = $this->accountRepository->getAccount($this->getUser()->getCodice(), $_locale);
		$dataInizio = date($account->getDataIscrizione());

		list($annoIniziale, ,) = explode('-', $dataInizio);
		$annoIniziale = (int) $annoIniziale;
		$arrayAnni = [];
		for($i = $annoIniziale; (int) $annoAttuale >= $i; $i++){
			$arrayAnni[] = $i;
		}
		//endregion

		//region 1. verifichiamo l'anno che ci arriva, se non ci arriva abbiamo 0 di default e se è 0 gli passiamo l'anno attuale
		if($anno == 0){
			$anno = date('Y');
		}
		$anno = intval($anno);
		//endregion

		//region 2. prendo i dati dall'api con l'anno di riferimento
		$bonusAnnui = json_decode($this->json($this->repository->listaBonus($anno, $_locale))->getContent());
		//endregion

		//region 3 se la risposta api è diversa da null, lavoro i valori che mi arrivano dall'api
		if($bonusAnnui != null){
			$bonusMensili = [];
			$arrayTotali = [];
			$etichetteMesi = [];

			//region 3.1 conto i bonus e creo degli array per la pagina con i nomi dei bonus contenuti
			$nBonus = count($bonusAnnui[0]->bonus);
			for($i = 0; $i < $nBonus; $i++){
				$bonusMensili[] =
					[
						'name'         => 'default',
						'bonusMensile' => [],
					];
				$arrayTotali[] =
					[
						'name'   => 'default',
						'totale' => 0,
					];
			}
			//endregion

			$totaleDeiBonus = 0;

			foreach($bonusAnnui as $item){
				$totale = 0;
				$etichetteMesi[] = $item->meseTestoEsteso;

				//region 3.2 faccio la somma dei totali degli importi dei bonus
				foreach($item->bonus as $i => $bonus){
					$totale = $totale + $bonus->importo;

					$arrayTotali[$i] = [
						'name'   => $item->bonus[$i]->nome,
						'totale' => $bonus->importo + $arrayTotali[$i]['totale'],
					];

					$bonusMensili[$i]['name'] = $item->bonus[$i]->nome;
					$bonusMensili[$i]['bonusMensile'][] = $bonus->importo;
				}
				//endregion

				$totaleDeiBonus = $totaleDeiBonus + $totale;
			}

			return $this->render('pages/bonus/bonus.html.twig', [
				'arrayTotali'     => $arrayTotali, // array di oggetti contenenti i vari bonus (con importo totale per ogni singolo bonus nel periodo scelto)
				'totaleDeiBonus'  => $totaleDeiBonus, // importo totale della somma tra tutti i bonus per l'anno selezionato
				'bonusMensili'    => $bonusMensili, // importi suddivisi mese per mese di ogni singolo bonus
				'annoSelezionato' => $anno, // anno selezionato
				'anni'            => $arrayAnni, // array di anni a partire dalla data scelta a oggi
				'etichetteMesi'   => $etichetteMesi, // array di anni a partire dalla data scelta a oggi
			]);
		}

		//endregion
		return $this->render('error_pages/error_404.html.twig');
	}

	/**
	 * Restituisce il json formattato per il datatable nella pagina bonus tramite Ajax
	 *
	 */
	#[Route('/bonus-ajax/{anno}', name: 'bonus-ajax', methods: ['GET'])]
	public function bonusAjax(Request $request, int $anno = 0) : JsonResponse{
		if($anno == 0){
			$anno = date('Y');
		}
		$anno = intval($anno);

		try{
			$bonusAnnui = $this->repository->listaBonus($anno, $request->getLocale());
		}catch(Exception $exception){
			$bonusAnnui = [];
		}
		//region 1.0 se la risposta dall'api è valida, prendo i dati dall'api e creo i dati per il datatable
		$datatablesBonus = [];
		if($bonusAnnui != null){
			foreach($bonusAnnui as $item){
				$array = [];

				$item->getQualifica = '<span class=" ' . $item->getColore() . '">' . $item->getQualifica() . '</span>';

				if($item->isQualificato()){
					$attivo = '<span style="color:#33b18a;">' . "SI" . '</span>';
				}else{
					$attivo = '<span style="color:#922043;">' . "NO" . '</span>';
				}

				array_push($array, $item->getMeseTestoEsteso(), $item->getQualifica(), $attivo);

				foreach($item->getBonus() as $bonus){
					$array[] = $bonus['totale'];
				}

				$array[] = $item->getTotale();

				$array[] = substr($item->getMeseTestoEsteso(), 5, 2);
				$datatablesBonus[] = array_values($array);
			}
			//endregion
		}
		$countItems = count($datatablesBonus);
		$elencoBonus = array(
			'draw'            => time(),
			'recordsTotal'    => $countItems,
			'recordsFiltered' => $countItems,
			'data'            => $datatablesBonus,
		);

		return $this->json($elencoBonus);
	}
}
