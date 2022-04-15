<?php
declare(strict_types=1);

namespace App\Widget\FormGestisciAccount;

use App\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class FormGestisciAccount{
	public function __construct(
		private Environment       $twig,
		private AccountRepository $accountRepository,
	){
	}

	public function main(string $codice, $locale) : string{
		$oblio = null;
		$error_msg = '';
		try{
			$account = $this->accountRepository->getAccount($codice, $locale, '');
			$oblio = $account->getOblio();
		}catch(\Exception $e){
			$error_msg = $e->getMessage();
		}

		return $this->twig->render(
			'widgets/account/form_gestisci_account.html.twig', [
				'error_msg' => $error_msg,
				'oblio'     => $oblio,
			]
		);
	}
}
