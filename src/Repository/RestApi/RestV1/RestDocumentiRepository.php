<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\DocumentiRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\DocumentoCartellaViewModel;
use App\ViewModel\DocumentoFileViewModel;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestDocumentiRepository implements DocumentiRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	private int $ttlForAlberoDocumenti;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForDocumenti,
		private string                 $locales,
	){
	}

	public function getDocumenti(string $_locale, int $id_cartella) : ?DocumentoCartellaViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallDocumenti($_locale, $id_cartella));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		$results = $results['data'];

		return new DocumentoCartellaViewModel($results['id'], $results['nome'], $results['descrizione'], $results['breadcrumb'], $results['cartella_superiore'], $results['files']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallDocumenti(string $_locale, int $id_cartella) : callable{
		return function(ItemInterface $item) use ($_locale, $id_cartella){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/documenti/cartella/' . $id_cartella);

			$item->expiresAfter($this->ttlForDocumenti);
			$item->tag($this->authenticatedCacheTag(self::TAG_DOCUMENTI . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_DOCUMENTI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
