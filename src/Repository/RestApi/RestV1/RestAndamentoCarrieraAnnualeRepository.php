<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\AndamentoAnnualeRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\AndamentoAnnualeCarrieraViewModel;
use Exception;
use Generator;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestAndamentoCarrieraAnnualeRepository implements AndamentoAnnualeRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForAndamentoCarrieraAnnuale,
		private string                 $locales,
		private LoggerInterface        $logger
	){
	}

	public function getCarrieraAnnuale(string $locale, string $filtroColonnaOrdinamento, string $filtroDirezioneOrdinamento, string $pag, string $items) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallCarriera($locale, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $pag, $items));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage(), 1,);
			throw new Exception($exception->getMessage(), $exception->getCode());}

		foreach($results['data'] as $item){
			yield new AndamentoAnnualeCarrieraViewModel($item['id'], $item['mese'], $item['qualifica'], $item['style'], $item['attivo'], $item['condizioni'], $item['punti']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallCarriera(string $locale, string $filtroColonnaOrdinamento, string $filtroDirezioneOrdinamento, string $pag, string $items) : callable{
		return function(ItemInterface $item) use ($locale, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $pag, $items) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carriere/andamento-annuale' . '?locale=' . $locale . '&ordinamento=' . $filtroColonnaOrdinamento . '&direzione=' . $filtroDirezioneOrdinamento . '&items=' . $items . '&pag=' . $pag);

			$item->expiresAfter($this->ttlForAndamentoCarrieraAnnuale);
			$item->tag($this->authenticatedCacheTag(self::TAG_ANDAMENTO_ANNUALE . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ANDAMENTO_ANNUALE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}


