<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\EwalletRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\EwalletMovimentiViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestEwalletRepository implements EwalletRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForEwallet,
		private string                 $locales,
	){
	}

	public function getMovimenti() : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMovimenti());
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new EwalletMovimentiViewModel($item['data_operazione'], $item['operazione'], $item['costo'], $item['dettaglio'], $item['tipo_operazione'], $item['colore'], $item['visibile'], $item['costo_operazione']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallMovimenti() : callable{
		return function(ItemInterface $item) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/ewallet/movimenti');

			$item->expiresAfter($this->ttlForEwallet);
			$item->tag($this->authenticatedCacheTag(self::TAG_EWALLET));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_EWALLET)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getPagamenti() : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallPagamenti());
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new EwalletMovimentiViewModel($item['data_operazione'], $item['operazione'], $item['costo'], $item['dettaglio'], $item['tipo_operazione'], $item['colore'], $item['visibile'], $item['costo_operazione']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallPagamenti() : callable{
		return function(ItemInterface $item) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/ewallet/pagamenti');

			$item->expiresAfter($this->ttlForEwallet);
			$item->tag($this->authenticatedCacheTag(self::TAG_EWALLET));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_EWALLET)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}


