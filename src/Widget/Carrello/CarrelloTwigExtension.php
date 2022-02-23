<?php
declare(strict_types=1);

namespace App\Widget\Carrello;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class CarrelloTwigExtension extends AbstractExtension{
	public function __construct(private Carrello $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('carrello', [$this, 'main']),
		];
	}

	public function main(string $locale) : Markup{
		return new Markup($this->widget->main($locale), 'UTF-8');
	}
}