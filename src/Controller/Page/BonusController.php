<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\BonusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller per la pagina dei BONUS
 */
class BonusController extends AbstractController{
	/**
	 * @param \App\Repository\BonusRepository $repository
	 */
	public function __construct(
		private BonusRepository $repository,

	){
	}

	/**
	 * Mostra i dati relativi ai bonus nell'anno scelto,se l'anno non è stato selezionato di defaul passiamo l'anno corrente
	 *
	 * @param int $id
	 * id in questo caso è l'anno,poichè per comodità enlle traduzioni passiamo id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/bonus/{anno}', name: 'bonus', methods: ['GET'])]
	public function bonusView(string $_locale, int $anno = 0) : Response{
		//region creo gli anni da '$dataInizio' ad '$annoAttuale'FR
		$annoAttuale = date('Y');
		$dataInizio = date('2020-01-01');
		list($annoIniziale, $mese, $giorno) = explode('-', $dataInizio);
		$t_stamp_1 = '';
		$mese = (int) $mese;
		$giorno = (int) $giorno;
		$annoIniziale = (int) $annoIniziale;
		$arrayAnni = [];
		for($i = $annoIniziale; (int) $annoAttuale >= $i; $i++){
			array_push($arrayAnni, $i);
		}
		//endregion

		//region1 verifichiamo l'anno che ci arriva,se non ci arriva abbiamo 0 di default e se è 0 gli passiamo l'anno attuale
		if($anno == 0){
			$anno = date('Y');
		}
		$anno = intval($anno);
		//endregion

		//region2 prendo i dati dall'api con l'anno di riferimento
		$bonusAnnuiJson = $this->json($this->repository->listaBonus($anno, $_locale))->getContent();
		$bonusAnnui = json_decode($bonusAnnuiJson);
		//endregion

		//region3.0 se la risposta api è diversa da null, lavoro i valori che mi arrivano dall'api
		if($bonusAnnui != null){
			$bonusMensili = [];
			$arrayTotali = [];

			//region3.1 conto i bonus e creo degli array per la pagina con i nomi dei bonus contenuti
			$nBonus = count($bonusAnnui[0]->bonus);
			for($i = 0; $i < $nBonus; $i++){
				$arrayTotali[] =
					[
						'name'   => 'default',
						'totale' => 0,
					];
				$bonusMensili[] =
					[
						'name'         => 'default',
						'bonusMensile' => [],
					];
			}
			//endregion

			$totaleDeiBonus = 0;

			foreach($bonusAnnui as $item){
				$totale = 0;

				//region3.2 faccio la somma dei totali degli importi dei bonus
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
				'arrayTotali'     => $arrayTotali,//array di oggetti contenenti i vari bonus(con importo totale per ogni singolo bonus nel periodo scelto)
				'totaleDeiBonus'  => $totaleDeiBonus,//importo totale della somma tra tutti i bonus per l'anno selezionato
				'bonusMensili'    => $bonusMensili,//importi suddivisi mese per mese di ogni singolo bonus
				'annoSelezionato' => $anno,//anno selezionato
				'anni'            => $arrayAnni,//array di anni a partire dalla data scelta ad oggi
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
	public function bonusAjax(Request $request,int $anno = 0) : JsonResponse{
		if($anno == 0){
			$anno = date('Y');
		}
		$anno = intval($anno);

		$bonusAnnui = $this->repository->listaBonus($anno,$request->getLocale());

		//region1.0 se la risposta dall'api è valida, prendo i dati dall'api e creo i dati per il datatable
		if($bonusAnnui != null){
			$datatablesBonus = [];
			$totaleDeiBonus = 0;
			foreach($bonusAnnui as $item){
				$array = [];

				$item->getQualifica = '<span class=" ' . $item->getColore() . '">' . $item->getQualifica() . '</span>';

				if($item->isQualificato() == true){
					$attivo = '<span style="color:#33b18a;">' . "SI" . '</span>';
				}else{
					$attivo = '<span style="color:#922043;">' . "NO" . '</span>';
				}

				array_push($array, $item->getMeseTesto(), $item->getQualifica(), $attivo);

				foreach($item->getBonus() as $bonus){
					$array[] = $bonus['totale'];
				}

				$array[] = $item->getTotale();

				$array[] = substr($item->getMese(), 5, 2);
				$datatablesBonus[] = array_values($array);
			}
			//endregion
		}else{
			$datatablesBonus = [];
		}

		$elencoBonus = array(
			'draw'            => 1,
			'recordsTotal'    => 12,
			'recordsFiltered' => 12,
			'data'            => $datatablesBonus,
		);

		return $this->json($elencoBonus);
	}
}