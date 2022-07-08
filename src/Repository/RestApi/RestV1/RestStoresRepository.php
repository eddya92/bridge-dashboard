<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\StoresRepository;
use App\Service\Json;
use App\ViewModel\StoreViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestStoresRepository implements StoresRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForStores,
		private string                 $locales,
	){
	}

	public function getStore() : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallSellingzone());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new StoreViewModel($item['id'], $item['codice'], $item['insegna'], $item['listino'], $item['selling-zone'], $item['foto'], $item['logo'], $item['tipi_ordine']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallSellingzone() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/store', ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForStores);
			$item->tag($this->authenticatedCacheTag(self::TAG_STORES));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_STORES)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
