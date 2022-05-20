<?php
declare(strict_types=1);

namespace App\Controller\Widget\Top5;

use App\Repository\Top5Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Top5Controller extends AbstractController{
	public function __construct(private Top5Repository $repository){
	}

	/**
	 *
	 * @return Response
	 */
	#[Route('/top5-ajax/{utenza}/{anno}/{mese}', name: 'top5-ajax', defaults: ['anno' => '', 'mese' => ''], methods: ['GET'])]
	public function top5Ajax($utenza, $anno, $mese){
		$topUltimiIscritti = [];
		$topReclutatori = [];
		$topVendite = [];

		$users = $this->repository->getTop5($utenza, $anno, $mese);

		//region i dati presi dal repository li predispongo per la visualizzazione del widget top5(vendite)(reclutatori)(ultimiIscritti)
		if($users != null){
			foreach($users as $user){
				switch($user->getCategoria()){
					case 'top_vendite':
						$topVendite[] = $user;
						break;
					case 'top_reclutatori':
						$topReclutatori[] = $user;
						break;
					case 'top_ultimi_iscritti':
						$topUltimiIscritti[] = $user;
						break;
				}
			}
		}
		//endregion
		return $this->render('widgets/top5/top5_body_ajax.html.twig', [
			'year'              => $anno,
			'month'             => $mese,
			'topUltimiIscritti' => $topUltimiIscritti,
			'topReclutatori'    => $topReclutatori,
			'topVendite'        => $topVendite,
		]);
	}
}
