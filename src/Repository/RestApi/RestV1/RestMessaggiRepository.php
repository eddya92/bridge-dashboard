<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\MessaggiRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\MessaggioViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestMessaggiRepository implements MessaggiRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForMessaggi,
		private string                 $locales,
	){
	}

	/**
	 * @param string $locale
	 *
	 * @return \Generator
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function getMessaggi(string $locale) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMessaggi($locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception([false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']][1], $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new MessaggioViewModel($item['id'], $item['data'], $item['mittente'], $item['foto'], $item['da_leggere'], $item['titolo'], $item['testo'], $item['id_messaggio_precedente'], $item['id_messaggio_successivo']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallMessaggi(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/messaggi/messaggio?locale=' . $locale);

			$item->expiresAfter($this->ttlForMessaggi);
			$item->tag($this->authenticatedCacheTag(self::TAG_MESSAGGGI . $locale));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MESSAGGGI . $locale)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @param int    $id
	 * @param string $locale
	 *
	 * @return \App\ViewModel\MessaggioViewModel
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function getMessaggio(int $id, string $locale) : MessaggioViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMessaggio($id, $locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage(),1,);
			throw new Exception([false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']][1], $exception->getCode());
		}

		$data = $results['data'];

		return new MessaggioViewModel($data['id'], $data['data'], $data['mittente'], $data['foto'], $data['da_leggere'], $data['titolo'], $data['testo'], $data['id_messaggio_precedente'], $data['id_messaggio_successivo']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallMessaggio(int $id, string $locale) : callable{
		return function(ItemInterface $item) use ($id, $locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/messaggi/messaggio/' . $id . '?locale=' . $locale);

			$item->expiresAfter(45);
			$item->tag($this->authenticatedCacheTag(self::TAG_MESSAGGGI . $id . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MESSAGGGI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritDoc
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function getUltimoMessaggio(string $locale) : ?MessaggioViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallUltimoMessaggio($locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception([false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']][1], $exception->getCode());
		}
		if($results['data']){
			$data = $results['data'][0];

			return new MessaggioViewModel($data['id'], $data['data'], $data['mittente'], $data['foto'], $data['da_leggere'], $data['titolo'], $data['testo'], $data['id_messaggio_precedente'], $data['id_messaggio_successivo']);
		}else{
			return null;
		}

	}

	/**
	 *
	 *
	 * @return callable
	 */
	public function apiCallUltimoMessaggio($locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/messaggi/messaggio' . '?locale=' . $locale);

			$item->expiresAfter(45);
			$item->tag($this->authenticatedCacheTag(self::TAG_MESSAGGGI . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MESSAGGGI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}



