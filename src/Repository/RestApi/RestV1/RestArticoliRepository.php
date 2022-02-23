<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\ArticoliRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\ArticoloPiuVendutoViewModel;
use App\ViewModel\ArticoloUltimoAcquistoViewModel;
use App\ViewModel\ArticoloViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestArticoliRepository implements ArticoliRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForArticle
	){
	}

	public function getArticoliPiuVenduti(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallArticoliPiuVenduti($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new ArticoloPiuVendutoViewModel($item['id'], $item['nome'], $item['codice'], $item['categoria'], $item['descrizione'], $item['foto']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallArticoliPiuVenduti(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/vendite/articoli-piu-venduti');

			$item->expiresAfter($this->ttlForArticle);
			$item->tag($this->authenticatedCacheTag(self::TAG_ARTICOLI_PIU_VENDUTI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ARTICOLI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getArticoliUltimiAcquisti(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallArticoliUltimiAcquisti($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new ArticoloUltimoAcquistoViewModel($item['id'], $item['nome'], $item['codice'], $item['categoria'], $item['descrizione'], $item['foto']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallArticoliUltimiAcquisti(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/vendite/articoli-ultimi-acquisti');

			$item->expiresAfter($this->ttlForArticle);
			$item->tag($this->authenticatedCacheTag(self::TAG_ARTICOLI_ULTIMI_ACQUISTI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ARTICOLI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getArticolo(string $_locale, int $id) : ?ArticoloViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallArticolo($_locale, $id));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$item = $results['data'];

		return new ArticoloViewModel($item['id'], $item['nome'], $item['categoria'], $item['prezzo'], $item['sconto'], $item['prezzo_scontato'], $item['codice'], $item['punti'], $item['tipo'], $item['descrizione'], $item['abstract'], $item['foto'], $item['varianti'], $item['quantita_multipla'], $item['quantita_max'], $item['quantita_stock']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallArticolo(string $_locale, int $id) : callable{
		return function(ItemInterface $item) use ($_locale, $id){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/magazzino/articolo/' . $id);

			$item->expiresAfter($this->ttlForArticle);
			$item->tag($this->authenticatedCacheTag(self::TAG_ARTICOLI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ARTICOLI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getArticoli(string $locale, array $filtriAttivi) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallArticoli($locale, $filtriAttivi));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new ArticoloViewModel($item['id'], $item['nome'], $item['categoria'], $item['prezzo'], $item['sconto'], $item['prezzo_scontato'], $item['codice'], $item['punti'], $item['tipo'], $item['descrizione'], $item['abstract'], $item['foto'], $item['varianti'], $item['quantita_multipla'], $item['quantita_max'], $item['quantita_stock']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallArticoli(string $locale, array $filtriAttivi) : callable{
		return function(ItemInterface $item) use ($locale, $filtriAttivi){
			$params = http_build_query($filtriAttivi);

			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/magazzino/articolo?' . $params);

			$item->expiresAfter($this->ttlForArticle);
			$item->tag($this->authenticatedCacheTag(self::TAG_ARTICOLI . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ARTICOLI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}



