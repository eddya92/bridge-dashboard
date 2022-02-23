<?php
declare(strict_types=1);

namespace App\Widget\NotiziePrincipali;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class NotiziePrincipaliTwigExtension extends AbstractExtension{
	public function __construct(private NotiziePrincipali $controller){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('notiziePrincipali', [$this, 'main']),
		];
	}

	public function main( string $_locale) : Markup{
		return new Markup($this->controller->main( $_locale)->getContent(), 'UTF-8');
	}
}
