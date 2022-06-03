<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\ContattiRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Error;
use App\Service\Json;
use App\ViewModel\ContattiViewModel;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestContattiRepository implements ContattiRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForContatti,
		private RequestStack           $requestStack,
		private string                 $locales,
	){
	}

	/**
	 * @inheritDoc
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function getContatti(string $_locale) : ContattiViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallContatti($_locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception([false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']][1], $exception->getCode());
		}

		$results = $results['data'];

		return new ContattiViewModel($results['telefono'], $results['cellulare']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallContatti(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/contatti');

			if($response->getStatusCode() != 200){
				return $response->getReasonPhrase();
			}

			$item->expiresAfter($this->ttlForContatti);
			$item->tag($this->authenticatedCacheTag(self::TAG_CONTATTI . "[" . $_locale . "]"));

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritDoc
	 *
	 * @return array
	 */
	public function aggiornaContatti(string $telefono, string $cellulare) : array{
		try{
			$results = $this->apiCallAggiornaContatti($telefono, $cellulare);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallAggiornaContatti(string $telefono, string $cellulare) : array{
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->put('/db-v1/utenti/contatti', [
					'form_params' => [
						'telefono'   => $telefono,
						'cellulare ' => $cellulare,
					],
				]);
		}catch(Throwable $exception){
			$message = $exception->getMessage();
			$message = Error::format($message);
			throw new Exception($message);
		}

		$lingue = explode(',', $this->locales);
		foreach($lingue as $lingua){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CONTATTI . "[" . $lingua . "]")]);
		}

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return [true, ''];
	}
}
