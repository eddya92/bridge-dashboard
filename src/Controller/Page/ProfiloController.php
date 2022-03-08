<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\ContattiRepository;
use App\Repository\DatiFiscaliRepository;
use App\Repository\IndirizziRepository;
use App\Repository\InquadramentoFiscaleRepository;
use App\Repository\NazioniRepository;
use App\Repository\PrivacyRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller gestione profilo
 */
class ProfiloController extends AbstractController{
	/**
	 * @param \App\Repository\IndirizziRepository            $indirizziSpedizioneRepository
	 * @param \App\Repository\AccountRepository              $accountRepository
	 * @param \App\Repository\PrivacyRepository              $privacyRepository
	 * @param \App\Repository\InquadramentoFiscaleRepository $inquadramentoFiscaleRepository
	 */
	public function __construct(
		private IndirizziRepository            $indirizziSpedizioneRepository,
		private AccountRepository              $accountRepository,
		private PrivacyRepository              $privacyRepository,
		private InquadramentoFiscaleRepository $inquadramentoFiscaleRepository,
		private NazioniRepository              $nazioniRepository,
		private DatiFiscaliRepository          $datiFiscaliRepository,
		private ContattiRepository             $contattiRepository,
	){
	}

	/**
	 * Vista relativa alle informazioni dell'account personale
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/account', name: 'account', methods: ['GET', 'POST'])]
	public function account() : Response{
		return $this->render('pages/profilo/account.html.twig', [
			'codice' => $this->getUser()->getCodice(),
		]);
	}

	/**
	 * Invio del form di aggiornamento dati dell'account personale
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/aggiorna-dati-account', name: 'aggiorna-dati-account', methods: ['GET', 'POST'])]
	public function aggiornaDatiAccount(Request $request) : Response{

		//Aggiornamento dati account(modifica password) non viene gestita da noi
		return $this->redirect($this->getParameter('modifica_password'));
		$vecchiaPassword = trim($request->get('vecchiaPassword', ''));
		$nuovaPassword = trim($request->get('nuovaPassword', ''));
		$confermaPassword = trim($request->get('confermaPassword', ''));

		try{
			[$result, $error_msg] = $this->accountRepository->aggiornaDatiAccount($vecchiaPassword, $nuovaPassword, $confermaPassword);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('account');
	}

	/**
	 * Invio della richiesta di oblio
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/richiedi-oblio-account', name: 'richiedi-oblio-account', methods: ['GET'])]
	public function richiediOblioAccount() : Response{
		try{
			[$result, $error_msg] = $this->accountRepository->richiediOblioAccount();
			if($result){
				$this->addFlash('success', 'Richiesta di oblio avvenuta con successo');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('account');
	}

	/**
	 * Vista relativa form per aggiungere/modificare o eliminare un indirizzo salvato
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/dati-indirizzo-spedizione', name: 'dati-indirizzo-spedizione', methods: ['GET'])]
	public function datiSpedizione(Request $request, $_locale){
		$id = (int) $request->get('id', 0);

		$indirizzi = [];
		$indirizzoSelezionato = null;
		$indirizziGenerator = $this->indirizziSpedizioneRepository->getElencoIndirizziSpedizioneSalvati($_locale);

		$nazioni = $this->nazioniRepository->getNazioni();

		if($nazioni == null){
			$nazioni = [];
		}

		if($indirizziGenerator != null){
			foreach($indirizziGenerator as $indirizzoEntity){
				if($id > 0){
					if($indirizzoEntity->getId() === $id){
						$indirizzoSelezionato = $indirizzoEntity;
					}else{
						if($indirizzoEntity->isPrincipale()){
							$indirizzoSelezionato = $indirizzoEntity;
						}
					}
				}
				$indirizzi[] = $indirizzoEntity;
			}
		}

		return $this->render('pages/profilo/dati_spedizione.html.twig', [
				'indirizzi'           => $indirizzi,
				'indirizzoPrincipale' => $indirizzoSelezionato,
				'nazioni'             => $nazioni,
			]
		);
	}

	/**
	 * Vista relativa form per aggiungere/modificare o eliminare un indirizzo salvato
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/modifica-indirizzo-spedizione', name: 'modifica-indirizzo-spedizione', methods: ['GET', 'POST'])]
	public function modificaIndirizzoSpedizione(Request $request, $_locale){
		$id = (int) $request->get('id', 0);

		$indirizzo = $this->indirizziSpedizioneRepository->getIndirizzoSpedizione($id, $_locale);
		$indirizziGenerator = $this->indirizziSpedizioneRepository->getElencoIndirizziSpedizioneSalvati($_locale);

		$indirizzi = [];
		if($indirizziGenerator != null){
			foreach($indirizziGenerator as $indirizzoEntity){
				$indirizzi[] = $indirizzoEntity;
			}
		}

		return $this->render('pages/profilo/modifica_dati_spedizione.html.twig', [
			'indirizzo'        => $indirizzo,
			'indirizzi'        => $indirizziGenerator,
			'indirizziSalvati' => $indirizzi,
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/{_locale}/elimina-indirizzo-spedizione', name: 'elimina-indirizzo-spedizione', methods: ['POST'])]
	public function eliminaIndirizzoSpedizione(Request $request){
		$id = (int) $request->get('id', 0);

		try{
			[$result, $error_msg] = $this->indirizziSpedizioneRepository->eliminaIndirizzoSpedizione($id);
			if($result){
				$this->addFlash('success', 'Indirizzo eliminato correttamente');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('dati-indirizzo-spedizione');
	}

	/**
	 * Submit del form per aggiornare i dati di spedizione
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/{_locale}/aggiorna-dati-spedizione', name: 'aggiorna-dati-spedizione', methods: ['GET', 'POST'])]
	public function aggiornaDatiSpedizione(Request $request){

		//la gestione dei dati di spedizione non è nostra
		return $this->redirect($this->getParameter('dati_spedizione'));
		$id = (int) $request->get('id', 0);
		$nome = trim($request->get('nome', ''));
		$cognome = trim($request->get('cognome', ''));
		$indirizzo = trim($request->get('indirizzo', ''));
		$numeroCivico = trim($request->get('numero_civico', ''));
		$cap = trim($request->get('cap', ''));
		$comune = trim($request->get('comune', ''));
		$provincia = trim($request->get('provincia', ''));
		$email = trim($request->get('email', ''));
		$numeroTelefono = trim($request->get('numero_telefono', ''));
		$note = trim($request->get('note', ''));
		$nazione = trim($request->get('IT', ''));
		$isPrincipale = $request->get('isPrincipale', false);
		$consegnaSabato = $request->get('consegna_sabato', false);
		if($consegnaSabato != ''){
			$consegnaSabato = true;
		}else{
			$consegnaSabato = false;
		}

		if($isPrincipale){
			$isPrincipale = true;
		}

		try{
			[$result, $error_msg] = $this->indirizziSpedizioneRepository->aggiornaDatiSpedizione($id, $nome, $cognome, $indirizzo, $numeroCivico, $cap, $comune, $provincia, $nazione, $email, $numeroTelefono, $note, $isPrincipale, $consegnaSabato);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('dati-indirizzo-spedizione');
	}

	/**
	 * Vista modifica privacy
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/privacy', name: 'privacy', methods: ['GET'])]
	public function privacy(){
		$generator = $this->privacyRepository->getPrivacy();
		$privacyPolicy = [];
		$permessi = [];
		$consensi = [];

		if($generator != null){
			foreach($generator as $consenso){
				$consensi[] = $consenso;
				if($consenso->getTipo() === 'permessi'){
					$permessi[] = $consenso;
				}elseif($consenso->getTipo() === 'privacy'){
					$privacyPolicy[] = $consenso;
				}
			}
		}

		return $this->render('pages/profilo/privacy.html.twig', [
			'privacyPolicy' => $privacyPolicy,
			'permessi'      => $permessi,
		]);
	}

	/**
	 * Aggiornamento dati Privacy
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/{_locale}/aggiorna-dati-privacy', name: 'aggiorna-dati-privacy', methods: ['POST'])]
	public function aggiornaDatiPrivacy(Request $request){
		$nomeConsenso = trim($request->get('name', ''));
		$valoreConsenso = trim($request->get('value', ''));
		$ipPrivacy = $request->getClientIp();

		try{
			[$result, $error_msg] = $this->privacyRepository->aggiornaDatiPrivacy($nomeConsenso, $valoreConsenso, $ipPrivacy);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('privacy');
	}

	/**
	 * Vista relativa all'inquadramento fiscale personale, dove è possibile modificare i propri dati bancari
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/pagamenti', name: 'pagamenti', methods: ['GET'])]
	public function pagamenti(string $_locale){
		$inquadramento = $this->inquadramentoFiscaleRepository->getInquadramentoFiscale($_locale);

		if($inquadramento == null){
			$this->addFlash('error', 'errore caricamento pagina riprovare piu tardi');

			return $this->redirectToRoute('ingresso');
		}

		return $this->render('pages/profilo/pagamenti.html.twig', [
			'inquadramento' => $inquadramento,
		]);
	}

	/**
	 * Submit aggiornamento dati bancari
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/{_locale}/aggiorna-dati-pagamento', name: 'aggiorna-dati-pagamento', methods: ['GET', 'POST'])]
	public function aggiornaDatiPagamento(Request $request){
		$iban = trim($request->get('iban'), '');
		$bankCode = trim($request->get('bankCode'), '');

		try{
			[$result, $error_msg] = $this->inquadramentoFiscaleRepository->aggiornaPagamenti($iban, $bankCode);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('pagamenti');
	}

	/**
	 * Vista relativa form per aggiungere/modificare o eliminare un indirizzo salvato
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('/{_locale}/dati-personali', name: 'dati-residenza', methods: ['GET'])]
	public function datiResidenza(Request $request, $_locale){
		$locale = $request->getLocale();
		$residenza = $this->accountRepository->getResidenza($locale);
		$account = $this->accountRepository->getAccount($this->getUser()->getCodice(), $_locale);
		$nazione = $account->getNazioneResidenza();
		$datiFiscali = $this->datiFiscaliRepository->getDatiFiscali($_locale);
		$contatti = $this->contattiRepository->getContatti($_locale);

		if($datiFiscali == null){
			$datiFiscali = [];
		}

		if($residenza == null){
			$residenza = [];
		}

		if($contatti == null){
			$contatti = [];
		}

		return $this->render('pages/profilo/dati_residenza.html.twig', [
				'nazione'     => $nazione,
				'residenza'   => $residenza,
				'datiFiscali' => $datiFiscali,
				'contatti'    => $contatti,
				'account'     => $account,
			]
		);
	}

	/**
	 * Vista relativa form per aggiungere/modificare o eliminare un indirizzo salvato
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	#[Route('aggiorna-dati-residenza', name: 'aggiorna-dati-residenza', methods: ['GET', 'POST'])]
	public function aggiornaDatiResidenza(Request $request){
		//Aggiornamento dati non viene gestito da noi
		return $this->redirect($this->getParameter('dati_residenza'));
		$id = (int) $request->get('id', 0);
		$nome = trim($request->get('nome', ''));
		$cognome = trim($request->get('cognome', ''));
		$indirizzo = trim($request->get('indirizzo', ''));
		$numeroCivico = trim($request->get('numero_civico', ''));
		$cap = trim($request->get('cap', ''));
		$comune = trim($request->get('comune', ''));
		$provincia = trim($request->get('provincia', ''));
		$nazione = $this->accountRepository->getAccount($this->getUser()->getCodice(), $request->getLocale())->getNazioneResidenza();
		$piva = trim($request->request->get('piva', ''));
		$pec = trim($request->request->get('pec', ''));
		$codice_univoco = trim($request->request->get('codice_univoco', ''));
		$codice_fiscale = trim($request->request->get('codice_fiscale', ''));
		$telefono = trim($request->request->get('telefono', ''));
		$cellulare = trim($request->request->get('cellulare', ''));

		try{
			[$result, $error_msg] = $this->accountRepository->aggiornaDatiResidenza($nome, $cognome, $indirizzo, $numeroCivico, $cap, $comune, $provincia, $nazione);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
			[$result, $error_msg] = $this->datiFiscaliRepository->aggiornaDatiFiscali($codice_fiscale, $piva, $pec, $codice_univoco);
			if($result){
				$this->addFlash('success', 'Aggiornamento dati fiscali avvenuto con successo');
			}else{
				throw new Exception($error_msg);
			}
			//	[$result, $error_msg] = $this->contattiRepository->aggiornaContatti($telefono, $cellulare);
			//	if($result){
			//		$this->addFlash('success', 'Aggiornamento contatti avvenuto con successo');
			//	}else{
			//		throw new Exception($error_msg);
			//	}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('dati-residenza');
	}
}