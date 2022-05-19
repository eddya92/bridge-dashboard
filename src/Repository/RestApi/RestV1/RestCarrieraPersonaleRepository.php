<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\CarrieraPersonaleRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\QualificaViewModel;
use Exception;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestCarrieraPersonaleRepository implements CarrieraPersonaleRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForCarrieraPersonale,
		private string                 $locales,
	){
	}

	public function getQualifiche(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallQualifiche($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new QualificaViewModel($item['id'], $item['nome'], $item['colore'], $item['qualifica_attuale'], $item['descrizione'], $item['regole']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallQualifiche(string $_locale) : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carriere/qualifiche');
				//->request('GET', '/db-v1/carriere/qualifiche?locale=' . $_locale);

			$item->expiresAfter($this->ttlForCarrieraPersonale);
			$item->tag($this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritdoc
	 */
	public function infoProssimoRank(string $_locale) : ?Generator{
		// TODO: Implement getCarriera() method.
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallInfoProssimoRank($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new QualificaViewModel($item['id'], $item['nome'], $item['colore'], $item['qualifica_attuale'], $item['descrizione'], $item['regole']);
		}
	}

	private function apiCallInfoProssimoRank(string $_locale){
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carriere/qualifiche');
			//->request('GET', '/db-v1/carriere/qualifiche?locale=' . $_locale);

			$item->expiresAfter($this->ttlForCarrieraPersonale);
			$item->tag($this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function confermaQualifica() : ?Generator{
		// TODO: Implement getCarriera() method.
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallConfermaQualifica());
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new QualificaViewModel($item['id'], $item['nome'], $item['colore'], $item['qualifica_attuale'], $item['descrizione'], $item['regole']);
		}
	}

	private function apiCallConfermaQualifica(){
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carriere/conferma-qualifica');
			//->request('GET', '/db-v1/carriere/qualifiche?locale=' . $_locale);

			$item->expiresAfter($this->ttlForCarrieraPersonale);
			$item->tag($this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
