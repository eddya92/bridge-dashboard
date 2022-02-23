<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\SaldoCreditiRepository;
use App\Service\Json;
use App\ViewModel\SaldoViewModel;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestSaldoCreditiRepository implements SaldoCreditiRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForSaldoCrediti

	){
	}

	public function getSaldo() : ?SaldoViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallSaldoCrediti());
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$item = $results['data'];

		return new SaldoViewModel($item['saldo_attuale'], $item['saldo_minimo'], $item['requisiti'], $item['numero_fattura'], $item['accrediti'], $item['data_ultimo_accredito'], $item['ultimo_aggiornamento'], $item['data_fattura'], $item['autorizzazione_richiesta_cashout'], $item['messaggio_autorizzazione'], $item['requisiti_soddisfatti']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallSaldoCrediti() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/ewallet/saldo');

			$item->expiresAfter($this->ttlForSaldoCrediti);
			$item->tag($this->authenticatedCacheTag(self::TAG_SALDO));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_SALDO)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
