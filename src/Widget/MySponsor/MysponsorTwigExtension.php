<?php
declare(strict_types=1);

namespace App\Widget\MySponsor;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class MysponsorTwigExtension extends AbstractExtension{
	public function __construct(private Mysponsor $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('mySponsor', [$this, 'main']),
		];
	}

	public function main() : Markup{
		return new Markup($this->widget->main(), 'UTF-8');
	}
}
