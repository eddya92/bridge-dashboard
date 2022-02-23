<?php
declare(strict_types=1);

namespace App\Widget\Widget2;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Widget2Controller extends AbstractController{
	/**
	 * @param string $foo
	 * @param string $bar
	 *
	 * @return Response
	 */
	public function main(string $foo, string $bar) : Response{
		return $this->render(
			'widgets/widget2/main.html.twig',
			[
				'esecuzione' => $bar . ' con qualcosaltro',
			]
		);
	}

	public function sidebar() : Response{
		return $this->render(
			'widgets/widget2/sidebar.html.twig',
			[
				'counter' => 2,
			]
		);
	}
}
