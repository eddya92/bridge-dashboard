<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\DocumentiPersonaliRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\DocumentoPersonaleViewModel;
use Exception;
use Generator;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use JsonException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestDocumentiPersonaliRepository implements DocumentiPersonaliRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	private int $ttlForDocumenti;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForDocumentiPersonali,
		private string                 $locales,
	){
	}

	/**
	 * @inheritDoc
	 *
	 * @throws JsonException,
	 * @throws InvalidArgumentException
	 */
	public function getDocumentiPersonali(string $locale) : Generator{
		//try{
		$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallDocumentiPersonali($locale));
		$results = Json::decode($cached);
		foreach($results['data'] as $item){
			yield new DocumentoPersonaleViewModel($item['nome'], $item['caricato'], $item['data'], $item['link'], $item['obbligatorio'], $item['tesserino'], $item['caricabile'], $item['descrizione'], $item['id']);
		}
		//}catch(Throwable $e){
		//	error_log('ERROREEEEE!: '. $e->getMessage());
		//	return null;
		//}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallDocumentiPersonali(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/documento?locale=' . $locale);

			$item->expiresAfter($this->ttlForDocumentiPersonali);
			$item->tag($this->authenticatedCacheTag(self::TAG_DOCUMENTI_PERSONALI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_DOCUMENTI_PERSONALI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritDoc
	 */
	public function caricaDocumentoPersonale(string $iddoc, string $base64doc, string $namedoc) : array{
		try{
			$results = $this->apiCallCaricaDocumentoPersonale($iddoc, $base64doc, $namedoc);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	/**
	 * @param string $iddoc
	 * @param string $base64doc
	 * @param string $namedoc
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function apiCallCaricaDocumentoPersonale(string $iddoc, string $base64doc, string $namedoc) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/utenti/documento', [
				'form_params' => [
					'idDocumento'     => $iddoc,
					'base64Documento' => $base64doc,
					'nomeDocumento'   => $namedoc,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @return array
	 */
	public function creaTesserino() : array{
		try{
			$results = $this->apiCallCreaTesserino();
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	/**
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function apiCallCreaTesserino() : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/utenti/genera-tesserino');

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}
}
