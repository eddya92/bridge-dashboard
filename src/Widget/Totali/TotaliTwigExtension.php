<?php
declare(strict_types=1);

namespace App\Widget\Totali;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class TotaliTwigExtension extends AbstractExtension{
	public function __construct(private Totali $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('totali', [$this, 'main']),
		];
	}

	public function main(string $utenza) : Markup{
		return new Markup($this->widget->main($utenza), 'UTF-8');
	}
}
