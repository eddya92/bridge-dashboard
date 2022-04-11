<?php
declare(strict_types=1);

namespace App\Widget\GraficoVendite;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class GraficoVenditeTwigExtension extends AbstractExtension{
	public function __construct(private GraficoVendite $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('graficoVendite', [$this, 'main']),
		];
	}

	public function main(string $utenza, string $locale) : Markup{
		return new Markup($this->widget->main($utenza, $locale), 'UTF-8');
	}
}
