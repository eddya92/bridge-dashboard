<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\SitoPersonaleRepository;
use App\Service\CacheKey;
use App\Service\Json;
use App\ViewModel\SitoPersonaleViewModel;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class RestSitoPersonaleRepository implements SitoPersonaleRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForSitoPersonale,
		private string                 $locales,
	){
	}

	public function getSitoPersonale(string $locale) : ?SitoPersonaleViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallSitoPersonale($locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$results = $results['data'];

		return new SitoPersonaleViewModel($results['intestazione'], $results['immagine'], $results['nominativo'], $results['descrizione'], $results['cellulare'], $results['email'], $results['facebook'], $results['instagram'], $results['twitter'], $results['youtube'], $results['qualifica'], $results['collaboratori'], $results['uri']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallSitoPersonale(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken($locale))
				->client()
				->request('GET', '/db-v1/utenti/sito-personale', ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForSitoPersonale);
			$item->tag($this->authenticatedCacheTag(self::TAG_SITO_PERSONALE . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_SITO_PERSONALE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function aggiornaSitoPersonale(string $titolo, string $descrizione, string $telefono, string $email, string $facebook, string $instagram, string $twitter, string $youtube, $immagine) : array{
		try{
			$results = $this->apiCallAggiornaSitoPersonale($titolo, $descrizione, $telefono, $email, $facebook, $instagram, $twitter, $youtube, $immagine);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	private function apiCallAggiornaSitoPersonale(string $titolo, string $descrizione, string $telefono, string $email, string $facebook, string $instagram, string $twitter, string $youtube, array $immagine) : array{
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('POST', '/db-v1/utenti/sito-personale', [
					'form_params'     => [
						'nominativo'   => $titolo,
						'descrizione'  => $descrizione,
						'cellulare'    => $telefono,
						'email'        => $email,
						'facebook'     => $facebook,
						'instagram'    => $instagram,
						'twitter'      => $twitter,
						'youtube'      => $youtube,
						'immagine'     => $immagine['content'],
						'nomeImmagine' => $immagine['name'],
					],
					'connect_timeout' => 10.00,
				]);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		//per invalidarlo
		$locales = explode(',', $this->locales);
		foreach($locales as $locale){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_SITO_PERSONALE . "[" . $locale . "]")]);
		}

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return [true, ''];
	}

	public function getMinisito(string $locale, string $uri) : ?SitoPersonaleViewModel{
		try{
			$cached = $this->cache->get(CacheKey::fromTrace(), $this->apiCallMinisito($locale, $uri));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$results = $results['data'];

		return new SitoPersonaleViewModel($results['intestazione'], $results['immagine'], $results['nominativo'], $results['descrizione'], $results['cellulare'], $results['email'], $results['facebook'], $results['instagram'], $results['twitter'], $results['youtube'], $results['qualifica'], $results['collaboratori'], $results['uri']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallMinisito(string $locale, string $uri) : callable{
		return function(ItemInterface $item) use ($locale, $uri){
			$response = $this->restApiConnection()
				->client()
				->request('GET', '/db-v1/utenti/minisito?minisito=' . $uri . '&locale=' . $locale, ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForSitoPersonale);
			$item->tag($this->authenticatedCacheTag(self::TAG_SITO_PERSONALE . "[" . $locale . "]"));

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
