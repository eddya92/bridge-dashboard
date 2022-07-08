<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\PrivacyRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\PermessoViewModel;
use App\ViewModel\PrivacyViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestPrivacyRepository implements PrivacyRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int $ttlForPrivacy,
	){
	}

	public function getPrivacy() : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallPrivacy());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $key => $item){
			if($key === 'permessi'){
				foreach($item as $valori){
					yield new PermessoViewModel($valori['id'], $key, $valori['nome'], $valori['descrizione'], $valori['link'], $valori['consenso']);
				}
			}elseif($key === 'privacy'){
				foreach($item as $valori){
					yield new PrivacyViewModel($valori['id'], $key, $valori['nome'], $valori['descrizione'], $valori['link'], $valori['dati_in_possesso'], $valori['consenso']);
				}
			}
		}
	}

	private function apiCallPrivacy() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()->withAuthentication($this->authenticationToken())->client()->request('GET', '/db-v1/utenti/dati-privacy');

			$item->expiresAfter($this->ttlForPrivacy);
			$item->tag($this->authenticatedCacheTag(self::TAG_PRIVACY));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_PRIVACY)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function aggiornaDatiPrivacy(string $campo, string $valore, string $ip) : array{
		try{
			$results = $this->apiCallAggiornaDatiPrivacy($campo, $valore, $ip);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	private function apiCallAggiornaDatiPrivacy(string $campo, string $valore, string $ip) : array{
		$response = $this->restApiConnection()->withAuthentication($this->authenticationToken())->client()->request('POST', '/db-v1/utenti/dati-privacy', ['form_params' => ['campo' => $campo, 'valore' => $valore, 'ip' => $ip,],]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}
}
