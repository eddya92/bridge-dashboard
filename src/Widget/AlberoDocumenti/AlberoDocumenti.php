<?php
declare(strict_types=1);

namespace App\Widget\AlberoDocumenti;

use App\Repository\DocumentiRepository;
use Twig\Environment;

final class AlberoDocumenti{
	public function __construct(private Environment $twig, private DocumentiRepository $repository,){
	}

	public function main(string $_locale, int $id_cartella) : string{
		$file = $this->repository->getDocumenti($_locale, $id_cartella);

		if($file != null){
			return $this->twig->render('widgets/albero_documenti/albero_documenti_show.html.twig',
				[
					"documento" => $file,
				]);
		}else{
			return 'Documento ' . $id_cartella . 'non trovato.';
		}
	}
}
