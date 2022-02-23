<?php
declare(strict_types=1);

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MultimediaGalleryController extends AbstractController{
	#[Route('/multimedia-gallery', name: 'multimedia-gallery', methods: ['GET'])]
	public function multimediaGallery(){
		return $this->render('pages/multimedia_gallery.html.twig');
	}
}