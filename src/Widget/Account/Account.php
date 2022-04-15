<?php
declare(strict_types=1);

namespace App\Widget\Account;

use App\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class Account{
	public function __construct(
		private Environment       $twig,
		private AccountRepository $repository
	){
	}

	public function main(string $codice, string $template = '', string $locale, bool $simulazione) : string{
		$template_twig = 'widgets/account/account.html.twig';
		if($template == 'sponsor'){
			$template_twig = 'widgets/account/sponsor.html.twig';
		}
		if($simulazione){
			$account = $this->repository->getAccount($codice, $locale);
			$account = $this->repository->getAccount($account->getSuperiore(), $locale);
		}else{
			$account = $this->repository->getAccount($codice, $locale);
		}

		if($account != null){
			return $this->twig->render(
				$template_twig, [
				'account' => $account,
			]);
		}else{
			return 'Account ' . $codice . ' non trovato.';
		}
	}
}
