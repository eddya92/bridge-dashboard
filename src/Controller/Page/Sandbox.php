<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Sandbox extends AbstractController{
	#[Route('/sandbox', name: 'sandbox', methods: ['GET'])]
	public function seconda() : Response{
		return $this->render('pages/sandbox.html.twig');
	}
}
