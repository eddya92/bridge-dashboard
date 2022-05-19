<?php
declare(strict_types=1);

namespace App\Widget\Account;

use App\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use function dd;

final class Account{
	public function __construct(
		private Environment       $twig,
		private AccountRepository $accountRepository
	){
	}

	public function main(string $codice, string $template, string $locale, bool $simulazione) : string{
		$template_twig = 'widgets/account/account.html.twig';
		if($template == 'sponsor'){
			$template_twig = 'widgets/account/sponsor.html.twig';
		}
		$account = $this->accountRepository->getAccount($codice, $locale);
		if($simulazione){
			$account = $this->accountRepository->getAccount($account->getSuperiore(), $locale);
		}

		if($account !== null){
			return $this->twig->render(
				$template_twig, [
				'account' => $account,
			]);
		}else{
			return 'Account ' . $codice . ' non trovato.';
		}
	}
}
