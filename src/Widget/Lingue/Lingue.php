<?php
declare(strict_types=1);

namespace App\Widget\Lingue;

use App\Repository\AccountRepository;
use App\Repository\LingueRepository;
use Exception;
use Twig\Environment;

final class Lingue{
	public function __construct(private Environment $twig, private LingueRepository $repository, private AccountRepository $accountRepository){
	}

	public function main(string $codice, string $locale) : string{
		try{
			$lingue = $this->repository->getLingue();
		}catch(Exception $exception){
			return $this->twig->render('pages/error_widgets/errore_widget_vuoto.html.twig');
		};

		return $this->twig->render('widgets/lingue/lingue.html.twig',
			['lingue' => $lingue,
			 'locale' => $this->accountRepository->getAccount($codice, $locale)->getLocale(),
			]);
	}
}
