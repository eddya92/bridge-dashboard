<?php
declare(strict_types=1);

namespace App\Widget\Top5;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class Top5TwigExtension extends AbstractExtension{
	public function __construct(private Top5 $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('top5', [$this, 'main']),
			new TwigFunction('top5_body_ajax', [$this, 'bodyAjax']),
		];
	}

	public function main() : Markup{
		return new Markup($this->widget->main(), 'UTF-8');
	}
}
