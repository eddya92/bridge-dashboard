<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\ModalitaPagamentoRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\ModalitaPagamentoViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestModalitaPagamentoRepository implements ModalitaPagamentoRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForMetodoPagamento,
		private string                 $locales,
	){
	}

	public function getModalitaPagamento(int $id_spedizione = 0, int $id_modsped = 0) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallMetodoPagamento($id_spedizione, $id_modsped));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new ModalitaPagamentoViewModel($item['id'], $item['tipo'], $item['spesa'], $item['foto']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallMetodoPagamento(int $id_spedizione = 0, int $id_modsped = 0) : callable{
		return function(ItemInterface $item) use ($id_spedizione, $id_modsped){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/modalita-pagamento/modalita-pagamento?id_spedizione=' . $id_spedizione . '&id_modsped=' . $id_modsped);

			$item->expiresAfter($this->ttlForMetodoPagamento);
			$item->tag($this->authenticatedCacheTag(self::TAG_MODALITA_PAGAMENTO));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_MODALITA_PAGAMENTO)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
