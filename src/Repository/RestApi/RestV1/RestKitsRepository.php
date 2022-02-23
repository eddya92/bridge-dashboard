<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\KitsRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\KitViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestKitsRepository implements KitsRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForkit
	){
	}

	public function getKits(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallKits($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new KitViewModel($item['id'], $item['nome'], $item['costo'], $item['valuta'], $item['formato_valuta'], $item['benefits'], $item['colore']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallKits(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/magazzino/kit');

			$item->expiresAfter($this->ttlForkit);
			$item->tag($this->authenticatedCacheTag(self::TAG_KITS . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_KITS)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
