<?php
declare(strict_types=1);

namespace App\Widget;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class ToStringExtension extends AbstractExtension{
	public function getFunctions() : array{
		return [
			new TwigFunction('toString', [$this, 'main']),
		];
	}

	public function main(int $num){
		$string = strval($num);

		return $string;
	}
}
