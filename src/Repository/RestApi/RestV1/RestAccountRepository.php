<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\AccountRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Error;
use App\Service\Json;
use App\ViewModel\AccountViewModel;
use App\ViewModel\ResidenzaViewModel;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestAccountRepository implements AccountRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	/**
	 * @param TagAwareCacheInterface $cache
	 * @param int                    $ttlForAccount
	 * @param RequestStack           $requestStack
	 * @param string                 $locales
	 * @param LoggerInterface        $logger
	 */
	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForAccount,
		private RequestStack           $requestStack,
		private string                 $locales,
		private LoggerInterface        $logger
	){
	}

	/**
	 * @inheritdoc
	 */
	public function getAccount(string $codice, string $locale) : AccountViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallAccount($codice, $locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$results = $results['data'];

		return new AccountViewModel($results['id'], $results['foto'], $results['nazione_residenza'], $results['ruolo'], $results['nome'], $results['cognome'], $results['nominativo'], $results['qualifica'], $results['codice'], $results['data_iscrizione'], $results['codice_fiscale'], $results['telefono'], $results['email'], $results['carriera'], $results['oblio'], $results['cellulare'], $results['superiore'], $results['locale']);
	}

	/**
	 * Chiamata effettiva all'api per ricevere l'account
	 *
	 * @return callable(ItemInterface): string
	 */
	private function apiCallAccount(string $codice, string $locale) : callable{
		return function(ItemInterface $item) use ($codice, $locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/account/' . $codice . '?locale=' . $locale, ['connect_timeout' => 10.00]);

			$this->logger->info('CHIAMATA GET ACCOUNT ', ['GET', '/db-v1/utenti/account/' . $codice . '?locale=' . $locale, [$response->getBody()]]);
			$item->expiresAfter($this->ttlForAccount);
			$item->tag($this->authenticatedCacheTag(self::TAG_ACCOUNT . $codice . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ACCOUNT)]);

			if($response->getStatusCode() != 200){
				return $response->getReasonPhrase();
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritDoc
	 *
	 * @return array
	 * @throws Exception|InvalidArgumentException
	 */
	public function aggiornaDatiAccount(string $vecchiaPassword, string $nuovaPassword, string $confermaPassword) : array{
		try{
			$results = $this->apiCallAggiornaDatiAccount($vecchiaPassword, $nuovaPassword, $confermaPassword);
		}catch(Exception $exception){
			error_log($exception->getMessage());

			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * Chiamata effettiva all api per aggiornare la password
	 *
	 * @param string $vecchiaPassword
	 * @param string $nuovaPassword
	 * @param string $confermaPassword
	 *
	 * @return array
	 * @throws Exception|InvalidArgumentException
	 */
	private function apiCallAggiornaDatiAccount(string $vecchiaPassword, string $nuovaPassword, string $confermaPassword) : array{
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->put('/db-v1/utenti/dati-account', [
					'form_params'     => [
						'password_old'  => $vecchiaPassword,
						'password_new'  => $nuovaPassword,
						'password_new2' => $confermaPassword,
					],
					'connect_timeout' => 10.00,
				]);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$this->logger->info('CHIAMATA AGGIORNA ACCOUNT ', ['GET', '/db-v1/utenti/dati-account' . 'form_params' => [
			'password_old'  => $vecchiaPassword,
			'password_new'  => $nuovaPassword,
			'password_new2' => $confermaPassword,
		], [$response->getBody()]]);

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return [true, ''];
	}

	/**
	 * @inheritDoc
	 *
	 * @return array
	 * @throws GuzzleException
	 */
	public function richiediOblioAccount() : array{
		try{
			$results = $this->apiCallRichiediOblioAccount();
		}catch(Exception $exception){
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * @return array
	 * @throws GuzzleException
	 */
	private function apiCallRichiediOblioAccount() : array{
		//TODO: passaggio variabili richieste "000.000.000.000"

		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/utenti/oblio-account', [
				'form_params'     => [
					'ip_address' => '000.000.000.000',
					'user_agent' => $this->requestStack->getCurrentRequest()->headers->get('User-agent'),
				],
				'connect_timeout' => 10.00,
			]);
		$this->logger->info('CHIAMATA RICHIESTA OBLIO ', ['GET', 'POST', '/db-v1/utenti/oblio-account', [$response->getBody()]]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @inheritDoc
	 *
	 * @return array
	 * @throws GuzzleException
	 */
	public function registrazioneUtente(string $codiceSponsor, string $nome, string $cognome, string $email, string $password, string $nazione, array $agreements) : array{
		try{
			$results = $this->apiCallRegistrazioneUtente($codiceSponsor, $nome, $cognome, $email, $password, $nazione, $agreements);
		}catch(Exception $exception){
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * Chiamata api per la registrazione Incaricato
	 *
	 * @return array
	 * @throws GuzzleException
	 */
	private function apiCallRegistrazioneUtente(string $codiceSponsor, string $nome, string $cognome, string $email, string $password, string $nazione, array $agreements) : array{
		$response = $this->restApiConnection()
			->client()
			->request('POST', '/db-v1/utenti/iscrizione', [
				'form_params'     => [
					'codice'     => $codiceSponsor,
					'nome'       => $nome,
					'cognome'    => $cognome,
					'email'      => $email,
					'password'   => $password,
					'nazione'    => $nazione,
					'agreements' => implode('|', $agreements),
				],
				'connect_timeout' => 10.00,
			]);

		$this->logger->info('CHIAMATA REGISTRAZIONE UTENTE ', ['POST', '/db-v1/utenti/iscrizione' . 'form_params' => [
			'codice'     => $codiceSponsor,
			'nome'       => $nome,
			'cognome'    => $cognome,
			'email'      => $email,
			'password'   => $password,
			'nazione'    => $nazione,
			'agreements' => implode('|', $agreements),
		], [$response->getBody()]]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @inheritDoc
	 *
	 * @return array
	 * @throws GuzzleException
	 */
	public function registrazioneCliente(string $codiceSponsor, string $nome, string $cognome, string $ragioneSociale, string $naturaGiuridica, string $email, string $password, string $nazione, array $agreements) : array{
		try{
			$results = $this->apiCallRegistrazioneCliente($codiceSponsor, $nome, $cognome, $ragioneSociale, $naturaGiuridica, $email, $password, $nazione, $agreements);
		}catch(Exception $exception){
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * Chiamata api per la registrazione Cliente
	 *
	 * @return array
	 * @throws GuzzleException
	 */
	private function apiCallRegistrazioneCliente(string $codiceSponsor, string $nome, string $cognome, string $ragioneSociale, string $naturaGiuridica, string $email, string $password, string $nazione, array $agreements) : array{
		$response = $this->restApiConnection()
			->client()
			->request('POST', '/db-v1/clienti/iscrizione', [
				'form_params'     => [
					'codice'           => $codiceSponsor,
					'nome'             => $nome,
					'cognome'          => $cognome,
					'ragione_sociale'  => $ragioneSociale,
					'natura_giuridica' => $naturaGiuridica,
					'email'            => $email,
					'password'         => $password,
					'nazione'          => $nazione,
					'agreements'       => implode('|', $agreements),
				],
				'connect_timeout' => 10.00,
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @inheritDoc
	 *
	 * @return \App\ViewModel\ResidenzaViewModel
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function getResidenza(string $locale) : ResidenzaViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallResidenza($locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage(), 1,);
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$results = $results['data'];

		return new ResidenzaViewModel($results['natura_giuridica'], $results['nome'], $results['cognome'], $results['nominativo'], $results['codice_fiscale'], $results['ragione_sociale'], $results['legale_rappresentante'], $results['provincia'], $results['cap'], $results['comune'], $results['indirizzo'], $results['numero_civico'], $results['indirizzo_completo'], $results['country_code']);
	}

	/**
	 * @param string $locale
	 *
	 * @return callable
	 */
	private function apiCallResidenza(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/residenza' . '?locale=' . $locale, ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForAccount);
			$item->tag($this->authenticatedCacheTag(self::TAG_ACCOUNT_RESIDENZA . '[' . $locale . ']'));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI_SPEDIZIONE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritDoc
	 *
	 * @throws InvalidArgumentException
	 */
	public function aggiornaDatiResidenza(string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione){
		try{
			$results = $this->apiCallAggiornaDatiResidenza($nome, $cognome, $indirizzo, $numeroCivico, $cap, $comune, $provincia, $nazione);
		}catch(Exception $exception){
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * Chiamata api per aggiornare la i dati di residenza
	 *
	 * @throws InvalidArgumentException
	 */
	private function apiCallAggiornaDatiResidenza(string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione){
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->put('/db-v1/utenti/residenza', [
					'form_params'     => [
						'nome'          => $nome,
						'cognome'       => $cognome,
						'indirizzo'     => $indirizzo,
						'numero_civico' => $numeroCivico,
						'cap'           => $cap,
						'comune'        => $comune,
						'provincia'     => $provincia,
						'nazione'       => $nazione,
					],
					'connect_timeout' => 10.00,
				]);
		}catch(Throwable $exception){
			$message = $exception->getMessage();
			$message = Error::format($message);
			throw new Exception($message);
		}

		$lingue = explode(',', $this->locales);
		foreach($lingue as $lingua){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ACCOUNT_RESIDENZA . "[" . $lingua . "]")]);
		}

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return $response;
	}

	/**
	 * @inheritDoc
	 *
	 * @throws InvalidArgumentException|GuzzleException
	 */
	public function inviaMailRecuperoPassword(string $email){
		try{
			$results = $this->apiCallInviaMailRecuperoPassword($email);
		}catch(Exception $exception){
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * @param string $email
	 *
	 * @throws GuzzleException
	 */
	private function apiCallInviaMailRecuperoPassword(string $email){
		$response = $this->restApiConnection()
			->client()
			->request('POST', '/db-v1/utenti/recupero-password', [
				'form_params'     => [
					'email' => $email,
				],
				'connect_timeout' => 10.00,
			]);

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return $response;
	}
}
