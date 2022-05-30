<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AndamentoAnnualeRepository;
use App\Repository\CarrieraPersonaleRepository;
use Exception;
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
	 * Mostra l'elenco delle qualifiche possibili, ed evidenzia la qualifica Attuale
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
		$order = $request->get('order', [['column' => 0, 'dir' => 'asc']]);
		$filtroDirezioneOrdinamento = "periodo";
		$filtroColonnaOrdinamento = "ASC";
		$pag = ($request->get('start', '0'));
		$items = ($request->get('length', '0'));

		switch($order[0]['column']){
			case 0:
				$filtroColonnaOrdinamento = 'periodo';
				break;
			case 1:
				$filtroColonnaOrdinamento = 'qualificaRaggiunta';
				break;
			case 2:
				$filtroColonnaOrdinamento = 'coinsPersonali';
				break;
			case 3:
				$filtroColonnaOrdinamento = 'coinsGruppo';
				break;
		}
		switch($order[0]['dir']){
			case 'asc':
				$filtroDirezioneOrdinamento = 'ASC';
				break;
			case 'desc':
				$filtroDirezioneOrdinamento = 'DESC';
				break;
		}

		try{
			$carrieraGenerator = $this->andamentoAnnualeRepository->getCarrieraAnnuale($locale, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $pag, $items);
		}catch(Exception $exception){
			$count = count($andamentoDatatable);
			$andamentoDatatable = [];
			$andamentoQualifica = array(
				'draw'            => time(),
				'recordsTotal'    => $count,
				'recordsFiltered' => $count,
				'data'            => $andamentoDatatable,
			);

			return $this->json($andamentoQualifica);
		}

		foreach($carrieraGenerator as $andamentoMensile){
			$array = [];
			$andamentoMensile->getQualifica = '<span class="' . $andamentoMensile->getStyle() . '">' . $andamentoMensile->getQualifica() . '</span>';
			$array[] = $andamentoMensile->getMese();
			$array[] = $andamentoMensile->getQualifica();

			foreach($andamentoMensile->getPunti() as $punti){
				$punti['punti'] = '<span>' . $punti['punti'] . '</span>';
				$array[] = $punti['punti'];
			}

			$array[] = $andamentoMensile->getId();
			$andamentoDatatable[] = array_values($array);
		}
		$count = count($andamentoDatatable);

		$andamentoQualifica = array(
			'draw'            => time(),
			'recordsTotal'    => $count,
			'recordsFiltered' => $count,
			'data'            => $andamentoDatatable,
		);

		return $this->json($andamentoQualifica);
	}

	/**
	 *
	 */
	#[Route('/conferma-qualifica', name: 'conferma-qualifica', methods: ['GET'])]
	public function confermaQualifica() : Response{
		try{
			$conferma = $this->carrieraPersonaleRepository->confermaQualificaBU();
			$this->addFlash('success', 'Qualifica confermata,ora sei un Business');

			return $this->redirectToRoute('ingresso');
		}catch(Exception $exception){
			$this->addFlash('error', 'Errore nella conferma della qualifica: ' . $exception->getMessage());

			return $this->redirectToRoute('ingresso');
		}
	}
}