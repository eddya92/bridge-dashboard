<?php
declare(strict_types=1);

namespace App\Widget\InfoProssimoRank;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class InfoProssimoRankTwigExtension extends AbstractExtension{
	public function __construct(private InfoProssimoRank $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('infoProssimoRank', [$this, 'main']),
		];
	}

	public function main(string $codice,string $locale) : Markup{
		return new Markup($this->widget->main($codice, $locale), 'UTF-8');
	}
}
