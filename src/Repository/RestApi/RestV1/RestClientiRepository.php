<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\ClientiRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\ClientiViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestClientiRepository implements ClientiRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForClienti,
		private string                 $locales,
	){
	}

	/**
	 * @inheritDoc
	 *
	 * @return Generator
	 */
	public function getClienti(string $_locale, string $ricercaGenerica, string $dataDal, string $dataAl) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallClienti($_locale, $ricercaGenerica, $dataDal, $dataAl));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new ClientiViewModel($item['data_iscrizione'], $item['codice_cliente'], $item['nominativo'], $item['email'], $item['telefono'], $item['punti_pc'], $item['numero_ordini']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallClienti(string $_locale, string $ricercaGenerica, string $dataDal, string $dataAl) : callable{
		return function(ItemInterface $item) use ($_locale, $dataAl, $dataDal, $ricercaGenerica){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/clienti' . '?ricerca_generica=' . $ricercaGenerica . '&data_dal=' . $dataDal . '&data_al=' . $dataAl, ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForClienti);
			$item->tag($this->authenticatedCacheTag(self::TAG_CLIENTI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CLIENTI)]);

			return (string) $response->getBody();
		};
	}
}
