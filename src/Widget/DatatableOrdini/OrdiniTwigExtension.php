<?php
declare(strict_types=1);

namespace App\Widget\DatatableOrdini;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class OrdiniTwigExtension extends AbstractExtension{
	public function __construct(private Ordini $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('paginaInteraOrdini', [$this, 'main']),
			new TwigFunction('datatable', [$this, 'datatable']),
		];
	}

	public function main(string $_locale) : Markup{
		return new Markup($this->widget->main($_locale), 'UTF-8');
	}

	public function datatable() : Markup{
		return new Markup($this->widget->datatable(), 'UTF-8');
	}
}
