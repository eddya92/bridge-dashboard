<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\TotaliRepository;
use App\Service\Json;
use App\ViewModel\TotaleViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestTotaliRepository implements TotaliRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForTotali
	){
	}

	public function getTotali(string $utenza) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallTotali($utenza));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new TotaleViewModel($item['titolo'], $item['valore'], $item['dettaglio'], $item['link'], $item['button']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallTotali(string $utenza) : callable{

		return function(ItemInterface $item) use ($utenza){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/totali?codice_utente_simulato=' . $utenza);

			$item->expiresAfter($this->ttlForTotali);
			$item->tag($this->authenticatedCacheTag(self::TAG_TOTALI . $utenza));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_TOTALI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
