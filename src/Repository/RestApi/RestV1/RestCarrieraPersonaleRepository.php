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

	public function getQualifiche(string $_locale) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallQualifiche($_locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage(), 1,);
			throw new Exception($exception->getMessage(), $exception->getCode());
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
				->request('GET', '/db-v1/carriere/qualifiche', ['connect_timeout' => 10.00]);
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
	public function infoProssimoRank(string $codice, string $locale) : array{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallInfoProssimoRank($codice, $locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results['data'];
	}

	private function apiCallInfoProssimoRank(string $codice, string $locale){
		return function(ItemInterface $item) use ($codice, $locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carriere/info-prossimo-rank?locale=' . $locale . '&codice_utente_simulato=' . $codice, ['connect_timeout' => 10.00]);

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

	public function confermaQualificaBU(){
		try{
			$results = $this->apiCallConfermaQualificaBU();
		}catch(Exception $exception){
			error_log($exception->getMessage(), 1,);
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	private function apiCallConfermaQualificaBU(){
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/carriere/conferma-qualifica-BU', ['connect_timeout' => 10.00]);

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRIERA_PERSONALE)]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}
}
