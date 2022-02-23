<?php
declare(strict_types=1);

namespace App\Widget\ElencoClienti;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class ElencoClientiTwigExtension extends AbstractExtension{
	public function __construct(private ElencoClienti $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('elencoClienti', [$this, 'main']),
		];
	}

	public function main() : Markup{
		return new Markup($this->widget->main(), 'UTF-8');
	}
}
