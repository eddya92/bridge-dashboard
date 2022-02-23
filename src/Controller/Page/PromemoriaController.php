<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PromemoriaController extends AbstractController{
	#[Route('promemoria', name: 'promemoria', methods: ['GET'])]
	public function promemoria(){
		return $this->render('pages/promemoria.html.twig');
	}
}