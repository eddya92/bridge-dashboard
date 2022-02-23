<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AcademyController extends AbstractController{
	#[Route('eventi', name: 'eventi', methods: ['GET'])]
	public function eventiView(){
		return $this->render('pages/academy/eventi.html.twig');
	}

	#[Route('le-mie-iscrizioni', name: 'le-mie-iscrizioni', methods: ['GET'])]
	public function leMieIscrizioniView(){
		return $this->render('pages/academy/le_mie_iscrizioni.html.twig');
	}
}