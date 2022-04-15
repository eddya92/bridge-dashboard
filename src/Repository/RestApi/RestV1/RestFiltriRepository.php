<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\FiltriRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\FiltriEsitoViewModel;
use App\ViewModel\FiltriTipoOrdineViewModel;
use App\ViewModel\FiltroCheckboxViewModel;
use App\ViewModel\FiltroLinkViewModel;
use App\ViewModel\FiltroRangeViewModel;
use App\ViewModel\FiltroSelectExtViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestFiltriRepository implements FiltriRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private                        $ttlForFiltri,
		private string                 $locales,

	){
	}

	public function getCategorieFiltri(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallgetCategorieFiltri($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			switch($item['tipo']){
				case 'link':
					yield new FiltroLinkViewModel((string) $item['id'], $item['tipo'], $item['nome'], $item['sotto_categorie']);
					break;
				case 'range':
					yield new FiltroRangeViewModel((string) $item['id'], $item['tipo'], $item['nome'], $item['prefix'], $item['min'], $item['max']);
					break;
				case 'checkbox':
					yield new FiltroCheckboxViewModel((string) $item['id'], $item['tipo'], $item['nome'], $item['valore']);
					break;
				case 'select_ext':
					yield new FiltroSelectExtViewModel((string) $item['id'], $item['tipo'], $item['nome'], $item['valori']);
					break;
			}
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallgetCategorieFiltri(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/filtri/shop');

			$item->expiresAfter($this->ttlForFiltri);
			$item->tag($this->authenticatedCacheTag(self::TAG_FILTRI_CATEGORIE . "[" . $_locale . "]" ));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_FILTRI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getFiltriTipoOrdine(string $_locale) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallFiltriTipoOrdine($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new FiltriTipoOrdineViewModel($item['id'], $item['filtro']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallFiltriTipoOrdine(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/filtri/tipologie-ordini');

			$item->expiresAfter($this->ttlForFiltri);
			$item->tag($this->authenticatedCacheTag(self::TAG_FILTRI_TIPO_ORDINE . "[" . $_locale ."]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_FILTRI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getFiltriEsito(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallFiltriEsito($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new FiltriEsitoViewModel($item['id'], $item['filtro']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallFiltriEsito(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/filtri/tipologie-esiti');

			$item->expiresAfter($this->ttlForFiltri);
			$item->tag($this->authenticatedCacheTag(self::TAG_FILTRI_ESITO . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_FILTRI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}


