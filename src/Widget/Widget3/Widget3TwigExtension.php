<?php
declare(strict_types=1);

namespace App\Widget\Widget3;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class Widget3TwigExtension extends AbstractExtension{
	public function __construct(private Widget3 $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('widget3_mainBody', [$this, 'main']),
		];
	}

	public function main(string $foo) : Markup{
		return new Markup($this->widget->main($foo), 'UTF-8');
	}
}
