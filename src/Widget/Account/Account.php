<?php
declare(strict_types=1);

namespace App\Widget\Account;

use App\Repository\AccountRepository;
use Exception;
use Twig\Environment;

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
		try{
			$account = $this->accountRepository->getAccount($codice, $locale);
			if($simulazione){
				$account = $this->accountRepository->getAccount($account->getSuperiore(), $locale);
			}
		}catch(Exception $exception){
			$template_twig = 'widgets/account/account_error.html.twig';

			return $this->twig->render(
				$template_twig, [
				'message' => 'Problemi nel caricamento dei dati dell\'account con codice: ' . $codice . '<br>' . 'Error ' . $exception->getCode() . ': ' . $exception->getMessage(),
			]);
		}

		return $this->twig->render(
			$template_twig, [
			'account' => $account,
		]);
	}
}
