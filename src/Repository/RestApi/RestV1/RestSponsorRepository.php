<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\SponsorRepository;
use App\Service\Json;
use App\ViewModel\SponsorViewModel;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestSponsorRepository implements SponsorRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForSponsor,
		private string                 $locales,
	){
	}

	public function getSponsor() : ?SponsorViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallSponsor());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$item = $results['data'];

		return new SponsorViewModel($item['nome'], $item['cognome'], $item['nominativo'], $item['qualifica'], $item['codice'], $item['email'], $item['telefono']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public
	function apiCallSponsor() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/sponsor', [c]);

			$item->expiresAfter($this->ttlForSponsor);
			$item->tag($this->authenticatedCacheTag(self::TAG_SPONSOR));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_SPONSOR)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
