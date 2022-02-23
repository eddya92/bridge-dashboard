<?php
declare(strict_types=1);

namespace App\Widget\DatatableOrdini;

use App\Repository\FiltriRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class Ordini extends AbstractController{
	public function __construct(
		private RequestStack     $request,
		private Environment      $twig,
		private FiltriRepository $FiltriRepository,
	){
	}

	public function main(string $_locale) : string{
		$filtriEsito = $this->FiltriRepository->getFiltriEsito($_locale);
		$filtriTipoOrdine = $this->FiltriRepository->getFiltriTipoOrdine($_locale);

		if($filtriEsito == null){
			$filtriEsito = [];
		}

		if($filtriTipoOrdine == null){
			$filtriTipoOrdine = [];
		}

		return $this->twig->render(
			'widgets/ordini/pagina_intera_ordini.html.twig',
			[
				'filtriEsito'      => $filtriEsito,
				'filtriTipoOrdine' => $filtriTipoOrdine,
			]
		);
	}

	public function datatable() : string{
		/** @var Request $request */
		$request = $this->request->getCurrentRequest();

		return $this->twig->render(
			'widgets/ordini/pagina_intera_ordini.html.twig',
			[

			]
		);
	}
}
