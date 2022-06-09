<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\LingueRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\CacheKey;
use App\Service\Json;
use App\ViewModel\LingueViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestLingueRepository implements LingueRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache
	){
	}

	public function getLingue() : Generator{
		try{
			$cached = $this->cache->get(CacheKey::fromTrace(), $this->apiCallLingue());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception([false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']][1], $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new LingueViewModel($item['ID_lingua'], $item['Sigla'], $item['isAttiva']);
		}
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

			//$item->expiresAfter($this->ttlForMetodoPagamento);
			//$item->tag($this->authenticatedCacheTag(self::TAG_LINGUE));

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}

