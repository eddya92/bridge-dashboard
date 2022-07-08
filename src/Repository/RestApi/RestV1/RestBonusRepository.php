<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\BonusRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\BonusViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestBonusRepository implements BonusRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForBonus,
		private string                 $locales,
	){
	}

	/**
	 * @inheritdoc
	 */
	public function listaBonus(int $anno, string $locale) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallListaBonus($anno, $locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage(), 1,);
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $mese){
			yield new BonusViewModel($mese['mese'], $mese['mese_testo'], $mese['mese_testo_esteso'], $mese['qualifica'], $mese['colore'], $mese['livello'], $mese['qualificato'], $mese['bonus'], $mese['totale'], $mese['importo']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallListaBonus(int $anno, string $locale) : callable{
		if($anno == 0){
			$anno = date('Y');
		}

		return function(ItemInterface $item) use ($locale, $anno){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/guadagni/bonus?anno=' . $anno . '&locale=' . $locale, ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForBonus);
			$item->tag($this->authenticatedCacheTag(self::TAG_BONUS . $anno . $locale));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_BONUS)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
