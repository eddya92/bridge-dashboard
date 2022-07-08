<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\NazioniRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\CacheKey;
use App\Service\Json;
use App\ViewModel\AgreementViewModel;
use App\ViewModel\NazioneViewModel;
use Generator;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestNazioniRepository implements NazioniRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
	){
	}

	public function getNazioni() : Generator{
		try{
			$cached = $this->cache->get(CacheKey::fromTrace(), $this->apiCallNazioni());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new NazioneViewModel($item['codice'], $item['nome'], $item['icona'] ?? '');
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallNazioni() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->client()
				->request('GET', '/db-v1/nazioni/nazione');

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getAgreements(string $idNazione) : Generator{
		try{
			$this->cache->delete(CacheKey::fromTrace());
			$cached = $this->cache->get(CacheKey::fromTrace(), $this->apiCallAgreements($idNazione));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new AgreementViewModel($item['id'], $item['nome'], $item['descrizione'] ?? '', $item['link'] ?? '', $item['required'] ?? false);
		}
	}

	public function getUserAgreements(string $idNazione) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallAgreements($idNazione));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new AgreementViewModel($item['id'], $item['nome'], $item['descrizione'] ?? '', $item['link'] ?? '', $item['required'] ?? false);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallAgreements(string $idNazione) : callable{
		return function(ItemInterface $item) use ($idNazione){
			$response = $this->restApiConnection()
				->client()
				->request('GET', '/db-v1/nazioni/agreements?country_code=' . $idNazione);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallUserAgreements(string $idNazione) : callable{
		return function(ItemInterface $item) use ($idNazione){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/nazioni/agreements?country_code=' . $idNazione);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
