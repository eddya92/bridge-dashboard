<?php
declare(strict_types=1);

namespace App\Controller\Widget\Andamento;

use App\Repository\VenditeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Aggiorna il grafico dell'andamento nella pagina "ingresso", in base ai filtri selezionati
 */
final class AndamentoController extends AbstractController{
	public function __construct(private VenditeRepository $repository){
	}

	/**
	 * Aggiorna il grafico dell'andamento nella pagina "ingresso", in base ai filtri selezionati
	 */
	#[Route('/andamento-grafico/{utenza}/{dato}/{anno}/{mese}', name: 'andamento-grafico', methods: ['GET'])]
	public function andamentoGrafico(string $utenza, $dato, $anno, $mese = '') : JsonResponse{

		$venditeGenerator = $this->repository->vendite($utenza, $dato, $anno, $mese);

		if($venditeGenerator === null){
			$venditeGenerator = [];
		}

		return $this->json($venditeGenerator);
	}
}
