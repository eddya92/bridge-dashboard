<?php
declare(strict_types=1);

namespace App\Widget\Account;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class AccountTwigExtension extends AbstractExtension{
	public function __construct(private Account $widget){
	}

	public function getFunctions() : array{
		return [
			new TwigFunction('account', [$this, 'main']),
		];
	}

	public function main(string $codice, string $template = '',string $locale) : Markup{
		return new Markup($this->widget->main($codice, $template,$locale), 'UTF-8');
	}
}