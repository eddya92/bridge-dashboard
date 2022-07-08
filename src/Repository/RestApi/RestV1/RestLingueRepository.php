<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\LingueRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\LingueViewModel;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class RestLingueRepository implements LingueRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache
	){
	}

	public function getLingue() : array{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallLingue());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$lingue = [];
		foreach($results['data'] as $item){
			$lingue[] = new LingueViewModel($item['ID_lingua'], $item['Sigla'], $item['isAttiva']);
		}
		return $lingue;
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public
	function apiCallLingue() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->client()
				->request('GET', '/db-v1/lingue/lingue');

			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_LINGUE)]);
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_LINGUE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}

