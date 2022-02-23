<?php
declare(strict_types=1);

namespace App\Widget\Widget3;

use App\Repository\OneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use function count;
use function iterator_to_array;

final class Widget3{
	public function __construct(
		private RequestStack  $request,
		private Environment   $twig,
		private OneRepository $repository
	){
	}

	public function main(string $foo) : string{
		/** @var Request $request */
		$request = $this->request->getCurrentRequest();

		return $this->twig->render(
			'widgets/widget3/main.html.twig',
			[
				'host'  => $request->getHost(),
				'param' => $this->repository->get('a', 'b', 'c'),
				'list'  => $this->repository->listOfSomething('a', 'b'),
				'list2' => $this->repository->listOfSomething('a', 'b'),
				'count' => count(iterator_to_array($this->repository->listOfSomething('a', 'b'), false)),
				'list3' => $this->repository->listOfAnotherThing('a'),
				'list4' => $this->repository->listOfCombinedThings('a'),
			]
		);
	}
}
