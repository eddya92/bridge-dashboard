<?php
declare(strict_types=1);

namespace App\Widget\AlberoDocumenti;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class AlberoDocumentiTwigExtension extends AbstractExtension{
	public function __construct(private AlberoDocumenti $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('AlberoDocumentiAjax', [$this, 'main']),
		];
	}

	public function main(string $_locale, int $id_cartella) : Markup{
		return new Markup($this->widget->main($_locale, $id_cartella), 'UTF-8');
	}
}
