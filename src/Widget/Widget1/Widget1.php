<?php
declare(strict_types=1);

namespace App\Widget\Widget1;

use Twig\Environment;

final class Widget1{
	public function __construct(private Environment $twig){
	}

	public function main() : string{
		return $this->twig->render('widgets/widget1/main.html.twig');
	}
}
