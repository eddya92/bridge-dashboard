<?php
declare(strict_types=1);

namespace App\Controller\Widget\Ewallet;

use App\Repository\EwalletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Aggiorna datatable nella pagina ewallet,sia pagamenti che movimenti
 */
final class EwalletController extends AbstractController{
	public function __construct(private EwalletRepository $repository){
	}

	/**
	 * Aggiorna datatable movimenti nella pagina ewallet
	 */
	#[Route('/ewallet-movimenti-ajax', name: 'ewallet-movimenti-ajax', methods: ['GET'])]
	public function ewalletMovimentiAjax() : JsonResponse{
		//$movimenti = $this->json($this->repository->getPagamenti())->getContent();
		//$movimentiJson = json_decode($movimenti, true);
		$movimentiGenerator = $this->repository->getMovimenti();
		$registroMovimenti = [];

		//region se i dati dal repository sono validi,li imposto come li vuole il datatable,altrimenti []
		if($movimentiGenerator != null){
			foreach($movimentiGenerator as $item){
				if($item['tipo_operazione'] == 'accredito'){
					$item['costo'] = '<span style="color:' . $item['colore'] . ';">+ ' . $item['costo_operazione'] . '</span>';
				}else{
					$item['costo'] = '<span style="color:' . $item['colore'] . ';">- ' . $item['costo_operazione'] . '</span>';
				}
				if($item['visibile'] == '1'){
					$item['dettaglio'] = '<a href="' . $item['dettaglio'] . '"><button type="button" class="display-inherit btn btn-square btn-outline-light btn-sm text-dark">Fattura <i class="icon-import"></i></button></a>';
				}
				$registroMovimenti[] = array_values($item);
			}
		}
		//endregion

		$elencoMovimenti = array(
			'draw'            => 1,
			'recordsTotal'    => count($registroMovimenti),
			'recordsFiltered' => count($registroMovimenti),
			'data'            => $registroMovimenti,
		);

		return $this->json($elencoMovimenti);
	}

	/**
	 * Aggiorna datatable pagamenti nella pagina ewallet
	 */
	#[Route('/ewallet-pagamenti-ajax', name: 'ewallet-pagamenti-ajax', methods: ['GET'])]
	public function ewalletPagamentiAjax() : JsonResponse{
		//$movimenti = $this->json($this->repository->getPagamenti())->getContent();
		//$movimentiJson = json_decode($movimenti, true);
		$pagamentiGenerator = $this->repository->getPagamenti();
		$registroPagamenti = [];

		//region se i dati dal repository sono validi,li imposto come li vuole il datatable,altrimenti []
		if($pagamentiGenerator != null){
			foreach($pagamentiGenerator as $item){
				if($item['tipoOperazione'] == 'accredito'){
					$item['costo'] = '<span style="color:' . $item['colore'] . ';">+ ' . $item['costo_operazione'] . '</span>';
				}else{
					$item['costo'] = '<span style="color:' . $item['colore'] . ';">- ' . $item['costo_operazione'] . '</span>';
				}
				if($item['visibile'] == '1'){
					$item['dettaglio'] = '<a href="' . $item['dettaglio'] . '"><button type="button" class="display-inherit btn btn-square btn-outline-light btn-sm text-dark">Fattura <i class="icon-import"></i></button></a>';
				}
				$registroPagamenti[] = array_values($item);
			}
		}

		$elencoPagamenti = array(
			'draw'            => 1,
			'recordsTotal'    => count($registroPagamenti),
			'recordsFiltered' => count($registroPagamenti),
			'data'            => $registroPagamenti,
		);

		return $this->json($elencoPagamenti);
	}
}
