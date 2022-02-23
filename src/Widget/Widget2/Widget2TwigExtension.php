<?php
declare(strict_types=1);

namespace App\Widget\Widget2;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class Widget2TwigExtension extends AbstractExtension{
	public function __construct(private Widget2 $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('widget2_mainBody', [$this, 'main']),
			new TwigFunction('widget2_sidebar', [$this, 'sidebar']),
		];
	}

	public function main(string $foo, string $bar = 'due') : Markup{
		return new Markup($this->widget->main($foo, $bar), 'UTF-8');
	}

	public function sidebar() : Markup{
		return new Markup($this->widget->sidebar(), 'UTF-8');
	}
}
