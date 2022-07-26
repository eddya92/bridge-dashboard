<?php
declare(strict_types=1);

namespace App\Widget\Lingue;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class LingueTwigExtension extends AbstractExtension{
	public function __construct(private Lingue $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('lingue', [$this, 'main']),
		];
	}

	public function main(string $codice, string $locale) : Markup{
		return new Markup($this->widget->main($codice, $locale), 'UTF-8');
	}

}
