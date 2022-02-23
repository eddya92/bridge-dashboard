<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\OneRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\CacheKey;
use App\Service\Collection\Join;
use App\Service\Json;
use App\ViewModel\Top5UserViewModel;
use Generator;
use loophp\collection\Collection;
use loophp\collection\Contract\Collection as CollectionInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestOneRepository implements OneRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForOneList
	){
	}

	/**
	 * @inheritdoc
	 */
	public function get(string $userId, string $oneParam, string $anotherParam) : string{
		return $this->cache->get(
			CacheKey::fromTrace(),
			static function(ItemInterface $item) use ($userId, $oneParam, $anotherParam){
				// Do something with $userId, $oneParam and $anotherParam which gives some $result
				$result = 'value from remote';

				$item->expiresAfter(10);

				return $result;
			}
		);
	}

	/**
	 * @inheritdoc
	 */
	public function listOfSomething(string $userId, string $firstParm) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->singleCall($userId, $firstParm));
			$results = Json::decode($cached);
		}catch(Throwable $exception){
			return;
		}

		foreach($results['data'] as $item){
			yield $item['name'];
		}
	}

	/**
	 * @inheritdoc
	 */
	public function listOfAnotherThing(string $param) : Generator{
		try{
			$cachedOfFirstPart = $this->cache->get(
				$this->authenticatedCacheKey() . '_part_1',
				function(ItemInterface $item) use ($param) : string{
					$response = $this->restApiConnection()
						->withAuthentication($this->authenticationToken())
						->client()
						->request('GET', '/db-v1/list_a_part_1');

					$item->expiresAfter($this->ttlForOneList);

					return (string) $response->getBody();
				}
			);
			$resultsOfFirstPart = Json::decode($cachedOfFirstPart);

			foreach($resultsOfFirstPart['data'] as $item){
				yield new Top5UserViewModel($item['name'], $item['score']);
			}

			$cachedOfSecondPart = $this->cache->get(
				$this->authenticatedCacheKey() . '_part_2',
				function(ItemInterface $item) use ($param) : string{
					$response = $this->restApiConnection()
						->withAuthentication($this->authenticationToken())
						->client()
						->request('GET', '/list_a_part_2');

					$item->expiresAfter($this->ttlForOneList);

					return (string) $response->getBody();
				}
			);
			$resultsOfSecondPart = Json::decode($cachedOfSecondPart);

			foreach($resultsOfSecondPart['data'] as $item){
				yield new Top5UserViewModel($item['name'], $item['score']);
			}
		}catch(Throwable $exception){
			return;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function listOfCombinedThings(string $param) : Generator{
		try{
			$names = function(string $param) : CollectionInterface{
				$cached = $this->cache->get(
					$this->authenticatedCacheKey() . '_part_1',
					function(ItemInterface $item) use ($param) : string{
						$response = $this->restApiConnection()
							->withAuthentication($this->authenticationToken())
							->client()
							->request('GET', '/list_b_part_1');

						$item->expiresAfter($this->ttlForOneList);

						return (string) $response->getBody();
					}
				);

				return Collection::fromIterable(Json::decode($cached)['data'] ?? []);
			};

			$scores = function(string $param) : CollectionInterface{
				$cached = $this->cache->get(
					$this->authenticatedCacheKey() . '_part_2',
					function(ItemInterface $item) use ($param) : string{
						$response = $this->restApiConnection()
							->withAuthentication($this->authenticationToken())
							->client()
							->request('GET', '/list_b_part_2');

						$item->expiresAfter($this->ttlForOneList);

						return (string) $response->getBody();
					}
				);

				return Collection::fromIterable(Json::decode($cached)['data'] ?? []);
			};

			foreach(Join::of($names($param), $scores($param), 'id', 'id') as $item){
				yield new Top5UserViewModel($item['name'], $item['score']);
			}
		}catch(Throwable $exception){
			return;
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function singleCall(string $userId, string $firstParm) : callable{
		return function(ItemInterface $item) use ($userId, $firstParm) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/list');

			$item->expiresAfter($this->ttlForOneList);

			return (string) $response->getBody();
		};
	}

	//tentativi di scopiazzare Frangio

	/**
	 * @inheritdoc
	 */
	public function sales($anno) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->callSales($anno));
			$results = Json::decode($cached);
		}catch(Throwable $exception){
			return;
		}

		foreach($results['data'] as $item){
			yield $item;
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function callSales($anno) : callable{
		return function(ItemInterface $item) use ($anno) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/andamentoGrafico' . $anno);

			$item->expiresAfter($this->ttlForOneList);

			return (string) $response->getBody();
		};
	}
}
