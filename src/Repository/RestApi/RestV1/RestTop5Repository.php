<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\Top5Repository;
use App\Service\Json;
use App\ViewModel\Top5UserViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;
use function dd;
use function in_array;

final class RestTop5Repository implements Top5Repository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForTop5,
		private string                 $locales,
		private int                    $numeroDiTopN
	){
	}

	public function getTop5(string $utenza, string $anno, string $mese) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallListOfTop5($utenza, $anno, $mese));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$keysRichieste = ['top_ultimi_iscritti', 'top_reclutatori', 'top_vendite'];

		foreach($results['data'] as $key => $items){
			if(in_array($key, $keysRichieste)){
				foreach($items as $item){
					yield new Top5UserViewModel($key, $item['nome'], $item['cognome'], $item['nominativo'], $item['codice'], $item['foto'], $item['data_iscrizione'], $item['num_collaboratori'], $item['num_ordini']);
				}
			}
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallListOfTop5(string $utenza, string $anno, string $mese) : callable{
		return function(ItemInterface $item) use ($utenza, $anno, $mese){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/top5?anno=' . $anno . '&mese=' . $mese . '&codice_utente_simulato=' . $utenza . '&numero=' . $this->numeroDiTopN);

			$item->expiresAfter($this->ttlForTop5);
			$item->tag($this->authenticatedCacheTag(self::TAG_TOP5 . $anno . $mese . $utenza));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_TOP5)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
