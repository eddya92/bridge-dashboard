<?php
declare(strict_types=1);

namespace App\Widget\DettaglioOrdine;

use App\Repository\RestApi\RestV1\RestOrdiniRepository;
use App\ViewModel\DettaglioOrdineViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class DettaglioOrdine{
	public function __construct(
		private RequestStack         $request,
		private Environment          $twig,
		private RestOrdiniRepository $repository
	){
	}

	public function main(string $pagina, int $id) : string{
		/** @var Request $request */
		$request = $this->request->getCurrentRequest();

		return $this->twig->render(

			'widgets/dettaglio_ordine/dettaglio_ordine.html.twig',
			[
				'ordine' => $this->repository->getDettaglioOrdine($id),
				'host'   => $request->getHost(),
				'pagina' => $pagina,
			]
		);
	}
}


