<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\CarrelloRepository;
use App\Repository\DatiFiscaliRepository;
use App\Repository\IndirizziRepository;
use App\Repository\ModalitaPagamentoRepository;
use App\Repository\ModalitaSpedizioneRepository;
use App\Repository\NazioniRepository;
use App\ViewModel\DatiFiscaliViewModel;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller gestisce il checkout
 *
 * @ORM\Entity
 * @ORM\Table(name="checkout_controller")
 */
class CheckoutController extends AbstractController{
	public function __construct(
		private IndirizziRepository          $repositoryIndirizzi,
		private CarrelloRepository           $repositoryCarrello,
		private ModalitaPagamentoRepository  $repositoryMetodoPagamento,
		private ModalitaSpedizioneRepository $repositoryModalitaSpedizione,
		private NazioniRepository            $repositoryNazioni,
		private AccountRepository            $repositoryAccount,
		private DatiFiscaliRepository        $repositoryDatiFiscali,

	){
	}

	/**
	 * Mostra il contenuto della paginase qualcosa va in errore fa il redirect alla home
	 */
	#[Route('/{_locale}/checkout', name: 'checkout', methods: ['GET', 'POST'])]
	public function checkout(Request $request, $_locale) : Response{

		$indirizziGenerator = $this->repositoryIndirizzi->getElencoIndirizziSpedizioneSalvati($_locale);
		$carrello = $this->repositoryCarrello->getCarrello($_locale);
		$metodiPagamento = $this->repositoryMetodoPagamento->getModalitaPagamento((int) $request->request->get('id_spedizione', 0), (int) $request->request->get('id_modsped', 0));
		$modalitaSpedizione = $this->repositoryModalitaSpedizione->getModalitaSpedizione((int) $request->request->get('id_spedizione', 0));
		$nazioni = $this->repositoryNazioni->getNazioni();
		$totali = $this->repositoryCarrello->getTotali($_locale, (int) $request->request->get('id_spedizione', 0), (int) $request->request->get('id_modsped', 0), (int) $request->request->get('id_modpag', 0));
		$residenza = $this->repositoryAccount->getResidenza($request->getLocale());

		if($residenza === null || $residenza === ''){
			$residenza = '';
		}

		if($indirizziGenerator == null || $metodiPagamento == null || $modalitaSpedizione == null || $nazioni == null || $carrello == null){
			$this->addFlash('error', 'Si è verificato un errore al momento del checkout. Riprovare.');

			return $this->redirectToRoute('carrello');
		}
		if(count($carrello->getArticoli()) == 0){
			$this->addFlash('error', '. Non ci sono articoli nel carrello. Inizia a fare acquisti!');

			return $this->redirectToRoute('carrello');
		}

		$post = $request->request->all();
		$post['step'] = $post['step'] ?? 'fatturazione';
		$post['id_spedizione'] = $post['id_spedizione'] ?? 0;
		$post['id_modsped'] = $post['id_modsped'] ?? 0;
		$post['id_modpag'] = $post['id_modpag'] ?? 0;
		$post['note'] = $post['note'] ?? '';
		$post['Fatturazione_Natura_giuridica'] = $post['Fatturazione_Natura_giuridica'] ?? 'privato';
		$post['Fatturazione_ID_nazione'] = $post['Fatturazione_ID_nazione'] ?? $residenza->getCountryCode();
		$post['Fatturazione_Ragione_sociale'] = $post['Fatturazione_Ragione_sociale'] ?? '';
		$post['Fatturazione_Nome'] = $post['Fatturazione_Nome'] ?? '';
		$post['Fatturazione_Cognome'] = $post['Fatturazione_Cognome'] ?? '';
		$post['Fatturazione_Indirizzo'] = $post['Fatturazione_Indirizzo'] ?? '';
		$post['Fatturazione_Numero_civico'] = $post['Fatturazione_Numero_civico'] ?? '';
		$post['Fatturazione_CAP'] = $post['Fatturazione_CAP'] ?? '';
		$post['Fatturazione_Comune'] = $post['Fatturazione_Comune'] ?? '';
		$post['Fatturazione_Provincia'] = $post['Fatturazione_Provincia'] ?? '';
		$post['Fatturazione_Cellulare'] = $post['Fatturazione_Cellulare'] ?? '';
		$post['Fatturazione_Email'] = $post['Fatturazione_Email'] ?? '';
		$post['Fatturazione_CF'] = $post['Fatturazione_CF'] ?? '';
		$post['Fatturazione_PIVA'] = $post['Fatturazione_PIVA'] ?? '';
		$post['Fatturazione_PEC'] = $post['Fatturazione_PEC'] ?? '';
		$post['Fatturazione_Codice_univoco'] = $post['Fatturazione_Codice_univoco'] ?? '';

		$post = array_merge($post, [
			'carrello'           => $carrello,
			'metodiPagamento'    => $metodiPagamento,
			'modalitaSpedizione' => $modalitaSpedizione,
			'nazioni'            => $nazioni,
			'indirizzi'          => $indirizziGenerator,
			'totali'             => $totali,
			'residenza'          => $residenza,
		]);

		return $this->render('pages/checkout/checkout.html.twig',
			$post
		);
	}

	#[Route('checkout-invio', name: 'checkout-invio', methods: ['POST'])]
	public function checkoutInvio(Request $request) : Response{
		[$result, $errors, $id_ordine] = $this->repositoryCarrello->checkout($request->request->all());
		if($errors != ''){
			$this->addFlash('error', $errors);

			return $this->redirectToRoute('carrello');
		}

		//dd($id_ordine['id_ordine']);
		$this->addFlash('success', 'Ordine inserito con successo.');

		return $this->redirectToRoute('ordine-concluso', [
			'id' => $id_ordine['id_ordine'],
		]);
	}

	/**
	 * Submit del form per aggiornare i dati di spedizione
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/aggiorna-dati-spedizione-checkout', name: 'aggiorna-dati-spedizione-checkout', methods: ['POST'])]
	public function aggiornaDatiSpedizione(Request $request){
		$id = (int) $request->request->get('ID_Spedizione_hidden', 0);
		$nome = trim($request->request->get('Spedizione_nome', ''));
		$cognome = trim($request->request->get('Spedizione_cognome', ''));
		$nazione = trim($request->request->get('Spedizione_nazione', 'IT'));
		$indirizzo = trim($request->request->get('Spedizione_indirizzo', ''));
		$numeroCivico = trim($request->request->get('Spedizione_numero_civico', ''));
		$cap = trim($request->request->get('Spedizione_cap', ''));
		$comune = trim($request->request->get('Spedizione_comune', ''));
		$provincia = trim($request->request->get('Spedizione_provincia', ''));
		$numeroTelefono = trim($request->request->get('Spedizione_numero_telefono', ''));
		$email = trim($request->request->get('Spedizione_email', ''));
		$note = trim($request->request->get('Spedizione_note', ''));
		$isPrincipale = $request->request->get('isPrincipale', false);
		$consegnaSabato = $request->request->get('Spedizione_consegna_sabato', false);

		if($consegnaSabato != false){
			$consegnaSabato = true;
		}

		if($isPrincipale != false){
			$isPrincipale = true;
		}

		try{
			[$result, $error_msg] = $this->repositoryIndirizzi->aggiornaDatiSpedizione($id, $nome, $cognome, $indirizzo, $numeroCivico, $cap, $comune, $provincia, $nazione, $email, $numeroTelefono, $note, $isPrincipale, $consegnaSabato);
			if($result){
				$this->addFlash('success', 'Informazioni aggiornate correttamente.');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('checkout');
	}

	/**
	 * Submit del form per aggiornare i dati di spedizione
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/aggiorna-dati-personali-checkout', name: 'aggiorna-dati-personali-checkout', methods: ['POST'])]
	public function aggiornaDatiResidenza(Request $request){
		$residenza = $this->repositoryAccount->getResidenza($request->getLocale());

		$indirizzo = trim($request->request->get('Fatturazione_Indirizzo', ''));
		$numeroCivico = trim($request->request->get('Fatturazione_Numero_civico', ''));
		$cap = trim($request->request->get('Fatturazione_CAP', ''));
		$comune = trim($request->request->get('Fatturazione_Comune', ''));
		$provincia = trim($request->request->get('Fatturazione_Provincia', ''));

		$piva = trim($request->request->get('Fatturazione_PIVA', ''));
		$pec = trim($request->request->get('Fatturazione_PEC', ''));
		$codiceUnivoco = trim($request->request->get('Fatturazione_Codice_univoco', ''));

		$codice_fiscale = trim($request->request->get('codice_fiscale_datiFiscali', ''));
		$piva = trim($request->request->get('piva_datiFiscali', ''));
		$pec = trim($request->request->get('pec_datiFiscali', ''));
		$codice_univoco = trim($request->request->get('codice_univoco_datiFiscali', ''));

		try{
			[$result, $error_msg] = $this->repositoryAccount->aggiornaDatiResidenza($residenza->getNome(), $residenza->getCognome(), $indirizzo, $numeroCivico, $cap, $comune, $provincia, $residenza->getCountryCode());
			if($result){
				$this->addFlash('success', 'Indirizzo di residenza aggiunto correttamente.');
			}else{
				throw new Exception($error_msg);
			}
			[$result, $error_msg] = $this->repositoryDatiFiscali->aggiornaDatiFiscali($codice_fiscale, $piva, $pec, $codice_univoco);
			if($result){
				$this->addFlash('success', 'Aggiornamento dati fiscali avvbenuta con successo');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());
		}

		return $this->redirectToRoute('checkout');
	}
}