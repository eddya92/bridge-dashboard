<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PagineStaticheController extends AbstractController{
	#[Route('/{_locale}/content-privacy', name: 'content-privacy', methods: ['GET'])]
	public function privacyView(){
		return $this->render('pages/pagine_statiche/privacy.html.twig');
	}

	#[Route('/{_locale}/content-cookies', name: 'content-cookies', methods: ['GET'])]
	public function cookiesView(){
		return $this->render('pages/pagine_statiche/cookies.html.twig');
	}

	#[Route('/{_locale}/content-contatti', name: 'content-contatti', methods: ['GET'])]
	public function contattiView(){
		return $this->render('pages/pagine_statiche/contatti.html.twig');
	}
}
