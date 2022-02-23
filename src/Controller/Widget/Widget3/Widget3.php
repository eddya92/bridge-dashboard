<?php
declare(strict_types=1);

namespace App\Controller\Widget\Widget3;

use App\Repository\OneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class Widget3 extends AbstractController{
	public function __construct(private OneRepository $repository){
	}

	#[Route('/dev/someData', name: 'someData', methods: ['GET'])]
	public function someData() : JsonResponse{
		return $this->json($this->repository->listOfSomething('foo', 'bar'));
	}

	#[Route('/users', name: 'users', methods: ['GET'])]
	public function users() : JsonResponse{
		return $this->json($this->repository->listOfUsers('foo', 'bar'));
	}

	#[Route('/recruiters', name: 'recruiters', methods: ['GET'])]
	public function recruiters() : JsonResponse{
		return $this->json($this->repository->listOfUsers('foo', 'bar'));
	}

	#[Route('/last-subscribed', name: 'subscribed', methods: ['GET'])]
	public function lastSubscribed() : JsonResponse{
		return $this->json($this->repository->listOfUsers('foo', 'bar'));
	}
}
