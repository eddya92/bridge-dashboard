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
		private int                    $ttlForMessaggi
	){
	}

	public function getMessaggi(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMessaggi($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new MessaggioViewModel($item['id'], $item['data'], $item['mittente'], $item['foto'], $item['da_leggere'], $item['titolo'], $item['testo'], $item['id_messaggio_precedente'], $item['id_messaggio_successivo']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallMessaggi(string $_locale) : callable{

		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/messaggi/messaggio?locale=' . $_locale);

			$item->expiresAfter($this->ttlForMessaggi);
			$item->tag($this->authenticatedCacheTag(self::TAG_MESSAGGGI . $_locale));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MESSAGGGI . $locale)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getMessaggio(int $id, string $_locale) : ?MessaggioViewModel{

		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMessaggio($id, $_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$data = $results['data'];

		return new MessaggioViewModel($data['id'], $data['data'], $data['mittente'], $data['foto'], $data['da_leggere'], $data['titolo'], $data['testo'], $data['id_messaggio_precedente'], $data['id_messaggio_successivo']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallMessaggio(int $id,string $_locale) : callable{

		return function(ItemInterface $item) use ($id, $_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/messaggi/messaggio/' . $id . '?locale=' . $_locale);

			$item->expiresAfter(45);
			$item->tag($this->authenticatedCacheTag(self::TAG_MESSAGGGI . $id . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MESSAGGGI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}



