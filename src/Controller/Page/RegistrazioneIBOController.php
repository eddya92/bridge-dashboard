<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Repository\AccountRepository;
use App\Repository\ContattiRepository;
use App\Repository\DatiFiscaliRepository;
use App\Repository\IndirizziRepository;
use App\Repository\InquadramentoFiscaleRepository;
use App\Repository\KitsRepository;
use App\Repository\NazioniRepository;
use App\Repository\PrivacyRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\TrasformazioneInIBORepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gestione della pagina di registrazione
 */
final class RegistrazioneIBOController extends AbstractController{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private AccountRepository              $accountRepository,
		private NazioniRepository              $nazioniRepository,
		private KitsRepository                 $kitrepository,
		private IndirizziRepository            $indirizziSpedizioneRepository,
		private PrivacyRepository              $privacyRepository,
		private InquadramentoFiscaleRepository $inquadramentoFiscaleRepository,
		private DatiFiscaliRepository          $datiFiscaliRepository,
		private ContattiRepository             $contattiRepository,
		private TrasformazioneInIBORepository  $trasformazioneInIBORepository,
	){
	}

	/**
	 * Vista di trasformazione cliente/incaricato
	 */
	#[Route('/{_locale}/registrazione-ibo', name: 'registrazione-ibo', methods: ['GET'])]
	public function registrazioneIbo($_locale) : Response{
		$kits = $this->kitrepository->getKits($_locale);

		if($kits == null){
			$kits = [];
		}

		return $this->render(
			'pages/registrazione_ibo/registrazione_ibo.html.twig',
			[
				'kits' => $kits,
			]
		);
	}

	/**
	 * Vista di trasformazione cliente/incaricato
	 */
	#[Route('/{_locale}/dati-per-trasformazione', name: 'dati-per-trasformazione', methods: ['GET'])]
	public function viewForm(string $_locale) : Response{
		$residenza = $this->accountRepository->getResidenza($_locale);
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

		return $this->render('pages/registrazione_ibo/form_trasformazione_ibo.html.twig', [
				'nazione'     => $nazione,
				'residenza'   => $residenza,
				'datiFiscali' => $datiFiscali,
				'contatti'    => $contatti,
				'account'     => $account,
			]
		);
	}

	/**
	 * Vista di trasformazione cliente/incaricato
	 */
	#[Route('/{_locale}/esegui-registrazione-ibo', name: 'esegui-registrazione-ibo', methods: ['POST'])]
	public function trasformazioneIncaricato(Request $request) : Response{
		$indirizzo = trim($request->get('indirizzo', ''));
		$numeroCivico = trim($request->get('numero_civico', ''));
		$cap = trim($request->get('cap', ''));
		$comune = trim($request->get('comune', ''));
		$provincia = trim($request->get('provincia', ''));
		$piva = trim($request->request->get('piva', ''));
		$pec = trim($request->request->get('pec', ''));
		$codice_univoco = trim($request->request->get('codice_univoco', ''));
		$codice_fiscale = trim($request->request->get('codice_fiscale', ''));
		$telefono = trim($request->request->get('telefono', ''));
		$cellulare = trim($request->request->get('cellulare', ''));

		try{
			[$result, $error_msg] = $this->trasformazioneInIBORepository->trasformaInIbo(
				$this->getUser()->getCodice(),
				$codice_fiscale,
				$piva,
				$pec,
				$codice_univoco,
				$indirizzo,
				$numeroCivico,
				$cap,
				$comune,
				$provincia,
				$telefono,
				$cellulare
			);
			if($result){
				$this->addFlash('success', 'Effettua il login per vedere le modifiche.');

				return $this->redirectToRoute('logout');
			}else{
				throw new Exception($error_msg);
			}
		}catch(Exception $e){
			$this->addFlash('error', $e->getMessage());

			return $this->redirectToRoute('registrazione-ibo');
		}
	}
}
