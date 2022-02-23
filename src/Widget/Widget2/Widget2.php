<?php
declare(strict_types=1);

namespace App\Widget\Widget2;

use Twig\Environment;

class Widget2{
	public function __construct(private Environment $twig){
	}

	public function main(string $foo, string $bar) : string{
		return $this->twig->render(
			'widgets/widget2/main.html.twig',
			[
				'esecuzione' => $bar . ' con qualcosaltro',
			]
		);
	}

	public function sidebar() : string{
		return $this->twig->render(
			'widgets/widget2/sidebar.html.twig',
			[
				'counter' => 2,
			]
		);
	}
}
