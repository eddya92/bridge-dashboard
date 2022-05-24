<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\UtentiRepository;
use App\Service\Json;
use App\ViewModel\UtenteViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestUtentiRepository implements UtentiRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForUtenti,
		private string                 $locales,

	){
	}

	/**
	 * @inheritdoc
	 */
	public function allOfSearch(string $search = '') : Generator{
		$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallGetUtente($search));
		$results = Json::decode($cached);

		foreach($results['data'] as $item){
			yield new UtenteViewModel($item['id'], $item['codice'], $item['nominativo']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallGetUtente(string $cerca = '') : callable{
		return function(ItemInterface $item) use ($cerca){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/utente?cerca=' . $cerca . '&role_id=ibo');

			$item->expiresAfter($this->ttlForUtenti);
			$item->tag($this->authenticatedCacheTag(self::TAG_UTENTI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_UTENTI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
