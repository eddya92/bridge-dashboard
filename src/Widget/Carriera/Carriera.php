<?php
declare(strict_types=1);

namespace App\Widget\Carriera;

use App\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class Carriera{
	public function __construct(private Environment $twig, //private AccountRepository $accountRepository
		){
	}

	/**
	 * @return string
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function main(string $codice,string $locale) : string{

		//$account = $this->accountRepository->getAccount($codice, $locale);

		$messaggio = "widget ancora da definire";

		return $this->twig->render('pages/error_widgets/errore_widget_vuoto.html.twig',
			[
				'messaggio' => $messaggio,
			]);

		//	if($account != null){
		//  return $this->twig->render('widgets/carriera/carriera.html.twig', [
		//		//	'carriera' => $account->getCarriera(),
		//		//]);
		//	}else{
		//		return 'Account ' . $codice . ' non trovato.';
		//	}
	}
}
