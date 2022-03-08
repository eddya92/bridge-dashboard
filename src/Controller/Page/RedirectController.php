<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController{

	#[Route('redirect-wp-crea-ordine', name: 'redirect-wp-crea-ordine', methods: ['GET'])]
	public function creaOrdine(){
		return $this->redirect('https://it.wordpress.org/');
	}

	#[Route('redirect-wp-dati-personali', name: 'redirect-wp-dati-personali', methods: ['GET'])]
	public function datiResidenza(){
		return $this->redirect('https://it.wordpress.org/');
	}

	#[Route('redirect-wp-modifica-dati-personali', name: 'redirect-wp-modifica-dati-personali', methods: ['GET'])]
	public function modificaDatiResidenza(){
		return $this->redirect('https://it.wordpress.org/');
	}

	#[Route('redirect-wp-modifica-password', name: 'redirect-wp-modifica-password', methods: ['GET'])]
	public function modificaPassword(){
		return $this->redirect('https://it.wordpress.org/');
	}

	#[Route('redirect-wp-spedizioni', name: 'redirect-wp-spedizioni', methods: ['GET'])]
	public function spedizioni(){
		return $this->redirect('https://it.wordpress.org/');
	}

	#[Route('redirect-wp-inserisci-nuovo-cliente', name: 'redirect-wp-inserisci-nuovo-cliente', methods: ['GET'])]
	public function nuovoCliente(){
		return $this->redirect('https://it.wordpress.org/');
	}

}