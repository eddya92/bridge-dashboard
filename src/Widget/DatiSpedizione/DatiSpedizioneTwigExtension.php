<?php
declare(strict_types=1);

namespace App\Widget\DatiSpedizione;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class DatiSpedizioneTwigExtension extends AbstractExtension{
	public function __construct(private DatiSpedizione $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('datiSpedizione', [$this, 'main']),
		];
	}

	public function main(int $id) : Markup{
		return new Markup($this->widget->main($id), 'UTF-8');
	}
}
