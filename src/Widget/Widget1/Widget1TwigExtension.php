<?php
declare(strict_types=1);

namespace App\Widget\Widget1;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class Widget1TwigExtension extends AbstractExtension{
	public function __construct(private Widget1 $controller){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('widget1_main', [$this, 'main']),
		];
	}

	public function main() : Markup{
		return new Markup($this->controller->main(), 'UTF-8');
	}
}
