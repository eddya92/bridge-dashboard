<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TicketSystemController extends AbstractController{
	#[Route('/ticket-system', name: 'ticketSystem', methods: ['GET'])]
	public function ticketSystem(){
		return $this->render('pages/ticket_system.html.twig');
	}
}