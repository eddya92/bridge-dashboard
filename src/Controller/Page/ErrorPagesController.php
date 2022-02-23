<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorPagesController extends AbstractController{
	#[Route('/error404', name: 'error404', methods: ['GET'])]
	public function error404(){
		return $this->render('error_pages/error_404.html.twig');
	}

	#[Route('/error403', name: 'error403', methods: ['GET'])]
	public function error403(){
		return $this->render('error_pages/error_403.html.twig');
	}
}