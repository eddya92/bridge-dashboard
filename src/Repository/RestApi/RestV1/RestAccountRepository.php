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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

/**
 *
 */
final class RestAccountRepository implements AccountRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	/**
	 * @param \Symfony\Contracts\Cache\TagAwareCacheInterface $cache
	 * @param int                                             $ttlForAccount
	 * @param \Symfony\Component\HttpFoundation\RequestStack  $requestStack
	 */
	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForAccount,
		private RequestStack           $requestStack,
		private string                 $locales
	){
	}

	/**
	 * Restituisce un account in base al codice utente che viene passato, nella lingua che viene passata
	 *
	 *
	 * @param string $codice
	 * @param string $locale
	 *
	 * @return \App\ViewModel\AccountViewModel|null
	 */
	public function getAccount(string $codice, string $locale) : ?AccountViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallAccount($codice, $locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$results = $results['data'];

		return new AccountViewModel($results['id'], $results['foto'], $results['nazione_residenza'], $results['ruolo'], $results['nome'], $results['cognome'], $results['nominativo'], $results['qualifica'], $results['codice'], $results['data_iscrizione'], $results['codice_fiscale'], $results['telefono'], $results['email'], $results['carriera'], $results['oblio'], $results['cellulare'], $results['superiore']);
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
				->request('GET', '/db-v1/utenti/account/' . $codice . '?locale=' . $locale);

			$item->expiresAfter($this->ttlForAccount);
			$item->tag($this->authenticatedCacheTag(self::TAG_ACCOUNT . $codice . $locale));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ACCOUNT)]);

			if($response->getStatusCode() != 200){
				return $response->getReasonPhrase();
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * Aggiorna la password di un utente
	 *
	 * @param string $vecchiaPassword
	 * @param string $nuovaPassword
	 * @param string $confermaPassword
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function aggiornaDatiAccount(string $vecchiaPassword, string $nuovaPassword, string $confermaPassword) : array{
		try{
			$results = $this->apiCallAggiornaDatiAccount($vecchiaPassword, $nuovaPassword, $confermaPassword);
		}catch(BadResponseException $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
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
	 * @throws \Exception
	 */
	private function apiCallAggiornaDatiAccount(string $vecchiaPassword, string $nuovaPassword, string $confermaPassword) : array{
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->put('/db-v1/utenti/dati-account', [
					'form_params' => [
						'password_old'  => $vecchiaPassword,
						'password_new'  => $nuovaPassword,
						'password_new2' => $confermaPassword,
					],
				]);
		}catch(Throwable $exception){
			$message = $exception->getMessage();
			$message = Error::format($message);
			throw new Exception($message);
		}

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return [true, ''];
	}

	/**
	 * @return array
	 */
	public function richiediOblioAccount() : array{
		try{
			$results = $this->apiCallRichiediOblioAccount();
		}catch(Exception $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
		}

		return $results;
	}

	/**
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function apiCallRichiediOblioAccount() : array{
		//TODO: passaggio variabili richieste "000.000.000.000"

		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/utenti/oblio-account', [
				'form_params' => [
					'ip_address' => '000.000.000.000',
					'user_agent' => $this->requestStack->getCurrentRequest()->headers->get('User-agent'),
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @param string $codiceSponsor
	 * @param string $nome
	 * @param string $cognome
	 * @param string $email
	 * @param string $password
	 * @param string $nazione
	 * @param array  $agreements
	 *
	 * @return array
	 */
	public function registrazioneUtente(string $codiceSponsor, string $nome, string $cognome, string $email, string $password, string $nazione, array $agreements) : array{
		try{
			$results = $this->apiCallRegistrazioneUtente($codiceSponsor, $nome, $cognome, $email, $password, $nazione, $agreements);
		}catch(Exception $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
		}

		return $results;
	}

	/**
	 * @param string $codiceSponsor
	 * @param string $nome
	 * @param string $cognome
	 * @param string $email
	 * @param string $password
	 * @param string $nazione
	 * @param array  $agreements
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function apiCallRegistrazioneUtente(string $codiceSponsor, string $nome, string $cognome, string $email, string $password, string $nazione, array $agreements) : array{
		$response = $this->restApiConnection()
			->client()
			->request('POST', '/db-v1/utenti/iscrizione', [
				'form_params' => [
					'codice'     => $codiceSponsor,
					'nome'       => $nome,
					'cognome'    => $cognome,
					'email'      => $email,
					'password'   => $password,
					'nazione'    => $nazione,
					'agreements' => implode('|', $agreements),
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @param string $codiceSponsor
	 * @param string $nome
	 * @param string $cognome
	 * @param string $ragioneSociale
	 * @param string $naturaGiuridica
	 * @param string $email
	 * @param string $password
	 * @param string $nazione
	 * @param array  $agreements
	 *
	 * @return array
	 */
	public function registrazioneCliente(string $codiceSponsor, string $nome, string $cognome, string $ragioneSociale, string $naturaGiuridica, string $email, string $password, string $nazione, array $agreements) : array{
		try{
			$results = $this->apiCallRegistrazioneCliente($codiceSponsor, $nome, $cognome, $ragioneSociale, $naturaGiuridica, $email, $password, $nazione, $agreements);
		}catch(Exception $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
		}

		return $results;
	}

	/**
	 * @param string $codiceSponsor
	 * @param string $nome
	 * @param string $cognome
	 * @param string $ragioneSociale
	 * @param string $naturaGiuridica
	 * @param string $email
	 * @param string $password
	 * @param string $nazione
	 * @param array  $agreements
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function apiCallRegistrazioneCliente(string $codiceSponsor, string $nome, string $cognome, string $ragioneSociale, string $naturaGiuridica, string $email, string $password, string $nazione, array $agreements) : array{
		$response = $this->restApiConnection()
			->client()
			->request('POST', '/db-v1/clienti/iscrizione', [
				'form_params' => [
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
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @return \App\ViewModel\ResidenzaViewModel|null
	 */
	public function getResidenza(string $locale) : ?ResidenzaViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallResidenza($locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$results = $results['data'];

		return new ResidenzaViewModel($results['natura_giuridica'], $results['nome'], $results['cognome'], $results['nominativo'], $results['codice_fiscale'], $results['ragione_sociale'], $results['legale_rappresentante'], $results['provincia'], $results['cap'], $results['comune'], $results['indirizzo'], $results['numero_civico'], $results['indirizzo_completo'], $results['country_code']);
	}

	/**
	 * @return callable
	 */
	private function apiCallResidenza(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/residenza' . '?locale=' . $locale);

			$item->expiresAfter($this->ttlForAccount);
			$item->tag($this->authenticatedCacheTag(self::TAG_ACCOUNT . '[' . $locale . ']'));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI_SPEDIZIONE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @param string $nome
	 * @param string $cognome
	 * @param string $indirizzo
	 * @param string $numeroCivico
	 * @param string $cap
	 * @param string $comune
	 * @param string $provincia
	 * @param string $nazione
	 *
	 * @return array
	 */
	public function aggiornaDatiResidenza(string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione) : array{
		try{
			$results = $this->apiCallAggiornaDatiResidenza($nome, $cognome, $indirizzo, $numeroCivico, $cap, $comune, $provincia, $nazione);
		}catch(Exception $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
		}

		return $results;
	}

	/**
	 * @param string $nome
	 * @param string $cognome
	 * @param string $indirizzo
	 * @param string $numeroCivico
	 * @param string $cap
	 * @param string $comune
	 * @param string $provincia
	 * @param string $nazione
	 *
	 * @return array
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	private function apiCallAggiornaDatiResidenza(string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione) : array{
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->put('/db-v1/utenti/residenza', [
					'form_params' => [
						'nome'          => $nome,
						'cognome'       => $cognome,
						'indirizzo'     => $indirizzo,
						'numero_civico' => $numeroCivico,
						'cap'           => $cap,
						'comune'        => $comune,
						'provincia'     => $provincia,
						'nazione'       => $nazione,
					],
				]);
		}catch(Throwable $exception){
			$message = $exception->getMessage();
			$message = Error::format($message);
			throw new Exception($message);
		}

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ACCOUNT)]);

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return [true, ''];
	}

	/**
	 * @param string $email
	 *
	 * @return array
	 */
	public function inviaMailRecuperoPassword(string $email) : array{
		try{
			$results = $this->apiCallInviaMailRecuperoPassword($email);
		}catch(Exception $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
		}

		return $results;
	}

	/**
	 * @param string $email
	 *
	 * @return array
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	private function apiCallInviaMailRecuperoPassword(string $email) : array{
		$response = $this->restApiConnection()
			->client()
			->request('POST', '/db-v1/utenti/recupero-password', [
				'form_params' => [
					'email' => $email,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}
}
