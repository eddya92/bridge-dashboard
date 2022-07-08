<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\IndirizziRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\IndirizzoSpedizioneViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestIndirizziRepository implements IndirizziRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForIndirizzi,
		private string                 $locales,
	){
	}

	public function getElencoIndirizziSpedizioneSalvati(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallIndirizziSpedizioneSalvati($_locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new IndirizzoSpedizioneViewModel($item['id'], $item['nominativo'], $item['nazione'], $item['indirizzo'], $item['civico'], $item['comune'], $item['provincia'], $item['cap'], $item['email'], $item['numero_telefono'], $item['nota'], $item['consegna_sabato'], $item['is_principale'], $item['corriere'], $item['nome'], $item['cognome'], $item['ragione_sociale']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallIndirizziSpedizioneSalvati(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/indirizzi/indirizzo');

			$item->expiresAfter($this->ttlForIndirizzi);
			$item->tag($this->authenticatedCacheTag(self::TAG_INDIRIZZI . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI_SPEDIZIONE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getIndirizzoSpedizione(int $id, string $_locale) : ?IndirizzoSpedizioneViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallIndirizzo($id, $_locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$item = $results['data'];

		return new IndirizzoSpedizioneViewModel($item['id'], $item['nominativo'], $item['nazione'], $item['indirizzo'], $item['civico'], $item['comune'], $item['provincia'], $item['cap'], $item['email'], $item['numero_telefono'], $item['nota'], $item['consegna_sabato'], $item['is_principale'], $item['corriere'], $item['nome'], $item['cognome'], $item['ragione_sociale']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallIndirizzo(int $id, string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale, $id){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/indirizzi/indirizzo/' . $id);

			$item->expiresAfter($this->ttlForIndirizzi);
			$item->tag($this->authenticatedCacheTag(self::TAG_INDIRIZZI . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI_SPEDIZIONE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function aggiornaDatiSpedizione(
		int    $id,
		string $nome,
		string $cognome,
		string $indirizzo,
		string $numeroCivico,
		string $cap,
		string $comune,
		string $provincia,
		string $nazione,
		string $email,
		string $numeroTelefono,
		string $note,
		bool   $isPrincipale,
		bool   $consegnaSabato
	) : array{
		try{
			$results = $this->apiCallAggiornaDatiSpedizione($id, $nome, $cognome, $indirizzo, $numeroCivico, $cap, $comune, $provincia, $nazione, $email, $numeroTelefono, $note, $isPrincipale, $consegnaSabato);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	private function apiCallAggiornaDatiSpedizione(int $id, string $nome, string $cognome, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $nazione, string $email, string $numeroTelefono, string $note, bool $isPrincipale, bool $consegnaSabato) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request(($id > 0 ? 'PUT' : 'POST'), '/db-v1/indirizzi/indirizzo' . ($id > 0 ? '/' . $id : ''), [
				'form_params' => [
					'nome'            => $nome,
					'cognome'         => $cognome,
					'indirizzo'       => $indirizzo,
					'numero_civico'   => $numeroCivico,
					'cap'             => $cap,
					'comune'          => $comune,
					'provincia'       => $provincia,
					'nazione'         => $nazione,
					'email'           => $email,
					'cellulare'       => $numeroTelefono,
					'note'            => $note,
					'principale'      => $isPrincipale,
					'consegna_sabato' => $consegnaSabato,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}
		$locales = explode(',', $this->locales);
		foreach($locales as $locale){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI . "[" . $locale . "]")]);
		}

		return [true, ''];
	}

	public function eliminaIndirizzoSpedizione(int $id) : array{
		try{
			$results = $this->apiCallEliminaIndirizzoSpedizione($id);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallEliminaIndirizzoSpedizione(int $id) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('DELETE', '/db-v1/indirizzi/indirizzo/' . $id);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		$locales = explode(',', $this->locales);
		foreach($locales as $locale){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI . "[" . $locale . "]")]);
		}

		return [true, ''];
	}
}
