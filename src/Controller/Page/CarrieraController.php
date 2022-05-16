<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AndamentoAnnualeRepository;
use App\Repository\CarrieraPersonaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller di gestione degli eventi nella pagina Carriera Personale
 */
class CarrieraController extends AbstractController{
	/**
	 * @param \App\Repository\CarrieraPersonaleRepository $carrieraPersonaleRepository
	 * @param \App\Repository\AndamentoAnnualeRepository  $andamentoAnnualeRepository
	 */
	public function __construct(
		private CarrieraPersonaleRepository $carrieraPersonaleRepository,
		private AndamentoAnnualeRepository  $andamentoAnnualeRepository
	){
	}

	/**
	 * mostra l'elenco delle qualifiche possibili, ed evidenzia la qualifica Attuale
	 */
	#[Route('{_locale}/carriera', name: 'carriera', methods: ['GET'])]
	public function carrieraView(string $_locale) : Response{

		$carrieraGenerator = $this->carrieraPersonaleRepository->getQualifiche($_locale);

		if($carrieraGenerator != null){
			return $this->render('pages/carriera/carriera.html.twig', [
				'qualifiche' => $carrieraGenerator,
			]);
		}

		return $this->render('error_pages/error_404.html.twig');
	}

	/**
	 * Mostra lo storico delle qualifiche ottenute, in un datatable nella pagina con uri "/carriera"
	 */
	#[Route('/carriera-ajax', name: 'carriera-ajax', methods: ['GET'])]
	public function carrieraAjaxDatatable(Request $request) : Response{
		$andamentoDatatable = [];
		$locale = $request->get('locale');

		$carrieraGenerator = $this->andamentoAnnualeRepository->getCarrieraAnnuale($locale);

		if($carrieraGenerator != null){
			foreach($carrieraGenerator as $andamentoMensile){
				$array = [];
				$andamentoMensile->getQualifica = '<span class="' . $andamentoMensile->getStyle() . '">' . $andamentoMensile->getQualifica() . '</span>';
				$array[] = $andamentoMensile->getMese();
				$array[] = $andamentoMensile->getQualifica();

				foreach($andamentoMensile->getPunti() as $punti){
					$punti['punti'] = '<span>' . $punti['punti'] . '</span>';
					$array[] = $punti['punti'];
				}

				//	foreach($andamentoMensile->getCondizioni() as $condizione){
				//		$condizione = '<span style="color:#33b18a;">' . $condizione . '</span>';
				//
				//		$array[] = $condizione;
				//	}

				//	if($andamentoMensile->isAttivo()){
				//		$attivo = '<span style="color:#33b18a;">SI</span>';
				//	}else{
				//		$attivo = '<span style="color:#922043;">NO</span>';
				//	}

				//$array[] = $attivo;
				$array[] = $andamentoMensile->getId();
				$andamentoDatatable[] = array_values($array);
			}

			$count = count($andamentoDatatable);

			$andamentoQualifica = array(
				'draw'            => 1,
				'recordsTotal'    => $count,
				'recordsFiltered' => $count,
				'data'            => $andamentoDatatable,
			);

			return $this->json($andamentoQualifica);
		}else{
			$count = count($andamentoDatatable);
			$andamentoDatatable = [];
			$andamentoQualifica = array(
				'draw'            => 1,
				'recordsTotal'    => $count,
				'recordsFiltered' => $count,
				'data'            => $andamentoDatatable,
			);

			return $this->json($andamentoQualifica);
		}
	}


	/**
	 *
	 */
	#[Route('/conferma-qualifica', name: 'conferma-qualifica', methods: ['GET'])]
	public function confermaQualifica() : Response{

		$conferma = $this->carrieraPersonaleRepository->confermaQualifica();

		if($conferma === null ){
			$this->addFlash('error','Errore nella conferma della qualifica,riprovare');
			return $this->redirectToRoute('ingresso');
		}

		$this->addFlash('success', 'Qualifica confermata,ora sei un Business');

		return $this->redirectToRoute('ingresso');
	}
}