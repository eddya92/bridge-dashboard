<?php
declare(strict_types=1);

namespace App\Widget\DettaglioOrdine;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class DettaglioOrdiniTwigExtension extends AbstractExtension{
	public function __construct(private DettaglioOrdine $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('dettaglioOrdini', [$this, 'main']),
		];
	}

	public function main(string $pagina, int $id) : Markup{
		return new Markup($this->widget->main($pagina, $id), 'UTF-8');
	}
}
