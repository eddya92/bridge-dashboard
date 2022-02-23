<?php
declare(strict_types=1);

namespace App\Widget\Shop;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class ShopTwigExtension extends AbstractExtension{
	public function __construct(private Shop $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('shop', [$this, 'main']),
			new TwigFunction('shop_ajax', [$this, 'ajax']),
			new TwigFunction('articolo_shop', [$this, 'articoloShop']),
			new TwigFunction('', [$this, 'filtriShop']),
			new TwigFunction('filtriShopAjax', [$this, 'filtriShopAjax']),

		];
	}

	public function main($value = "") : Markup{
		return new Markup($this->widget->main($value), 'UTF-8');
	}

	public function ajax(string $locale, array $filtriAttivi) : Markup{
		return new Markup($this->widget->ajax($locale, $filtriAttivi), 'UTF-8');
	}

	public function articoloShop() : Markup{
		return new Markup($this->widget->articoloShop(), 'UTF-8');
	}

	public function filtriShop(string $locale) : Markup{
		return new Markup($this->widget->filtriShop($locale), 'UTF-8');
	}
}
