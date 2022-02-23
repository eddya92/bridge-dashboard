<?php
declare(strict_types=1);

namespace App\Widget\Carriera;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class CarrieraTwigExtension extends AbstractExtension{
	public function __construct(private Carriera $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('carriera', [$this, 'main']),
		];
	}

	public function main(string $codice,string $locale) : Markup{
		return new Markup($this->widget->main($codice, $locale), 'UTF-8');
	}
}
