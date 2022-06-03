<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\DatiFiscaliRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Error;
use App\Service\Json;
use App\ViewModel\DatiFiscaliViewModel;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestDatiFiscaliRepository implements DatiFiscaliRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForDatiFiscali,
		private RequestStack           $requestStack,
		private string                 $locales,
	){
	}

	/**
	 * @inheritDoc
	 */
	public function getDatiFiscali(string $_locale) : DatiFiscaliViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallDatiFiscali($_locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception([false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']][1], $exception->getCode());
		}

		$results = $results['data'];

		return new DatiFiscaliViewModel($results['codice_fiscale'], $results['piva'], $results['pec'], $results['codice_univoco']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallDatiFiscali(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/dati-fiscali');

			if($response->getStatusCode() != 200){
				return $response->getReasonPhrase();
			}

			$item->expiresAfter($this->ttlForDatiFiscali);
			$item->tag($this->authenticatedCacheTag(self::TAG_DATI_FISCALI . "[" . $_locale . "]"));

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritDoc
	 */
	public function aggiornaDatiFiscali(string $codiceFiscale, string $PIVA, string $PEC, string $codiceUnivoco) : array{
		try{
			$results = $this->apiCallAggiornaDatiFiscali($codiceFiscale, $PIVA, $PEC, $codiceUnivoco);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	/**
	 *
	 * @return array
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	private function apiCallAggiornaDatiFiscali(string $codiceFiscale, string $PIVA, string $PEC, string $codiceUnivoco) : array{
		try{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->put('/db-v1/utenti/dati-fiscali', [
					'form_params' => [
						'codice_fiscale' => $codiceFiscale,
						'piva'           => $PIVA,
						'pec'            => $PEC,
						'codice_univoco' => $codiceUnivoco,
					],
				]);
		}catch(Throwable $exception){
			$message = $exception->getMessage();
			$message = Error::format($message);
			throw new Exception($message);
		}

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_DATI_FISCALI)]);

		if($response->getStatusCode() != 200){
			throw new Exception($response->getReasonPhrase());
		}

		return [true, ''];
	}
}
