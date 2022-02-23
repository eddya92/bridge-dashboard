<?php
declare(strict_types=1);

namespace App\Widget\FormGestisciAccount;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class FormGestisciAccountTwigExtension extends AbstractExtension{
	public function __construct(private FormGestisciAccount $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('formGestisciAccount', [$this, 'main']),
		];
	}

	public function main(string $codice,string $locale) : Markup{
		return new Markup($this->widget->main($codice, $locale), 'UTF-8');
	}
}
