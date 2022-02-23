<?php
declare(strict_types=1);

namespace App\Widget\SaldoCrediti;

use App\Repository\SaldoCreditiRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class SaldoCrediti{
	public function __construct(
		private RequestStack           $request,
		private Environment            $twig,
		private SaldoCreditiRepository $repository

	){
	}

	public function main() : string{
		/** @var Request $request */
		$request = $this->request->getCurrentRequest();

		$saldo = $this->repository->getSaldo();
		if($saldo == null){
			$saldo = [];
		}

		return $this->twig->render(
			'widgets/saldo/saldo.html.twig', [

				'saldi' => $saldo,
			]

		);
	}
}
