<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\ModalitaPagamentoRepository;
use App\Repository\ModalitaSpedizioneRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\ModalitaSpedizioneViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestModalitaSpedizioneRepository implements ModalitaSpedizioneRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForModalitaSpedizione
	){
	}

	public function getModalitaSpedizione(int $id_spedizione = 0) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMetodoSpedizione($id_spedizione));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new ModalitaSpedizioneViewModel($item['id'], $item['tipo'], $item['spesa']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallMetodoSpedizione(int $id_spedizione = 0) : callable{
		return function(ItemInterface $item) use ($id_spedizione){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/modalita-spedizioni/modalita-spedizione?id_spedizione=' . $id_spedizione);

			$item->expiresAfter($this->ttlForModalitaSpedizione);
			$item->tag($this->authenticatedCacheTag(self::TAG_MODALITA_SPEDIZIONE));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MODALITA_SPEDIZIONE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
