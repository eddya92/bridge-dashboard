<?php
declare(strict_types=1);

namespace App\Widget\SaldoCrediti;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class SaldoCreditiTwigExtension extends AbstractExtension{
	public function __construct(private SaldoCrediti $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('saldoCredito', [$this, 'main']),
		];
	}

	public function main() : Markup{
		return new Markup($this->widget->main(), 'UTF-8');
	}
}
