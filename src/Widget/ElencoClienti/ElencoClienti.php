<?php
declare(strict_types=1);

namespace App\Widget\ElencoClienti;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class ElencoClienti{
	public function __construct(
		private Environment $twig,

	){
	}

	public function main() : string{
		return $this->twig->render(
			'widgets/elenco_clienti/elenco_clienti.html.twig',
		);
	}
}
