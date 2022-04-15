<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\FilesRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\DocumentoFileViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestFilesRepository implements FilesRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForDirectories,
		private string                 $locales,

	){
	}

	public function getDocumenti(string $locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallDocumenti($locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data']['files'] as $item){
			yield new DocumentoFileViewModel($item['id'], $item['nome'], $item['descrizione'], $item['cartella'], $item['numero_documenti'], $item['estensione'], $item['dimensione'], $item['link']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallDocumenti(string $locale) : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/documenti/cartella' );
				//->request('GET', '/db-v1/documenti/cartella?locale=' . $locale );

			$item->expiresAfter($this->ttlForDirectories);
			$item->tag($this->authenticatedCacheTag(self::TAG_FILES));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_FILES)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
