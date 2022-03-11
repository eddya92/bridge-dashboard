<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class Security extends AbstractController{
	public function __construct(
		private AccountRepository $accountRepository,
	){
	}

	#[Route('/login', name: 'login', methods: ['GET', 'POST'])]
	public function login(AuthenticationUtils $authenticationUtils) : Response{
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		//é false quando non viene gestito da noi(.env)
		if($this->getParameter('enable_login') === 'false'){
			return $this->redirect($this->getParameter('url_logout'));
		}

		if($error != ''){
			$this->addFlash('error', 'Credenziali non corrette, riprovare.');
		}

		return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
	}

	#[Route('/sso', name: 'sso', methods: ['GET'])]
	public function sso(AuthenticationUtils $authenticationUtils) : Response{
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		if($error != ''){
			$this->addFlash('error', 'Credenziali non corrette, riprovare.');
		}

		return $this->redirectToRoute('login');
	}

	#[Route('/logout', name: 'logout', methods: ['GET'])]
	public function logout() : Response{
		throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}

	#[Route('/forgot-password', name: 'forgotPassword', methods: ['GET'])]
	public function forgotPassword() : Response{
		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		return $this->render('security/forgot_password.html.twig');
	}

	#[Route('/forgot-password', name: 'forgotPasswordSend', methods: ['POST'])]
	public function forgotPasswordSend(Request $request) : Response{
		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		try{
			[$result, $error_msg] = $this->accountRepository->inviaMailRecuperoPassword($request->get('code_or_email', ''));
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');

				return $this->redirectToRoute('forgotPasswordSent');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());

			return $this->redirectToRoute('forgotPasswordSent');
		}
	}

	#[Route('/forgot-password-sent', name: 'forgotPasswordSent', methods: ['GET'])]
	public function forgotPasswordSent(Request $request) : Response{
		if($this->getUser()){
			return $this->redirectToRoute('ingresso');
		}

		return $this->render('security/forgot_password_sent.html.twig');
	}
}
