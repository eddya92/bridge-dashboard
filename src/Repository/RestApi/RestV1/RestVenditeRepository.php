<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\VenditeRepository;
use App\Service\Json;
use App\ViewModel\VenditeViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestVenditeRepository implements VenditeRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForVendite
	){
	}

	public function vendite($dato, $anno, $mese = '') : Generator{
		if($mese == 0){
			try{
				$cached = $this->cache->get($this->authenticatedCacheKey(), $this->callVenditeAnno($dato, $anno));
				$results = Json::decode($cached);
			}catch(Throwable){
				return null;
			}
			foreach($results as $item){
				yield new VenditeViewModel($item['totali'], $item['andamento']);
			}
		}else{
			try{
				$cached = $this->cache->get($this->authenticatedCacheKey(), $this->callVenditeMese($dato, $anno, $mese));
				$results = Json::decode($cached);
			}catch(Throwable){
				return null;
			}

			foreach($results as $item){
				yield new VenditeViewModel($item['totali'], $item['andamento']);
			}
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function callVenditeAnno($dato, $anno) : callable{
		return function(ItemInterface $item) use ($anno, $dato) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/vendite/andamento-grafico?tipo=' . $dato . '&anno=' . $anno);

			$item->expiresAfter($this->ttlForVendite);
			$item->tag($this->authenticatedCacheTag(self::TAG_VENDITE));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_VENDITE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function callVenditeMese($dato, $anno, $mese) : callable{
		return function(ItemInterface $item) use ($anno, $dato, $mese) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/vendite/andamento-grafico?tipo=' . $dato . '&anno=' . $anno . '&mese=' . $mese);

			$item->expiresAfter($this->ttlForVendite);
			$item->tag($this->authenticatedCacheTag(self::TAG_VENDITE));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_VENDITE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
