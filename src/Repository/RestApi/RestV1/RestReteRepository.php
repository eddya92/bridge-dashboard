<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\ReteRepository;
use App\Service\Json;
use App\ViewModel\AlberoUnilevelVistaViewModel;
use Generator;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestReteRepository implements ReteRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForRete,
		private string                 $locales,
	){
	}

	public function getAlberoViste($locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallGetAlberoViste($locale));
			$results = Json::decode($cached);
		}catch(Throwable
		){
			return null;
		}

		foreach($results['data'] as $item){
			yield new AlberoUnilevelVistaViewModel((int) $item['id'], $item['nome'], (bool) $item['principale'], (int) $item['id_utente'], $item['nome_utente'], $item['livelli'], $item['mese'], $item['altezza'], $item['larghezza'], $item['orientamento'], $item['punti']);
		}
	}

	private function apiCallGetAlberoViste(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/vista-albero?' . 'locale=' . $locale);

			$item->expiresAfter($this->ttlForRete);
			$item->tag($this->authenticatedCacheTag(self::TAG_VISTE_ALBERI . "[" . $locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_VISTE_ALBERI)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function eliminaAlberoVista(int $id) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('DELETE', '/db-v1/utenti/vista-albero/' . $id);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}
		$locales = explode(',', $this->locales);
		foreach($locales as $locale){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_VISTE_ALBERI . "[" . $locale . "]")]);
		}

		return [true, ''];
	}

	public function salvaAlberoVista(int $principale, string $nome, int $idUtente, int $livelli, string $mese, string $altezza, string $larghezza, string $orientamento, string $punti, int $idVista) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/utenti/vista-albero', [
				'form_params' => [
					'idVista'      => $idVista,
					'principale'   => $principale,
					'nome'         => $nome,
					'idUtente'     => $idUtente,
					'livelli'      => $livelli,
					'mese'         => $mese,
					'altezza'      => $altezza,
					'larghezza'    => $larghezza,
					'orientamento' => $orientamento,
					'punti'        => $punti,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		$locales = explode(',', $this->locales);
		foreach($locales as $locale){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_VISTE_ALBERI . "[" . $locale . "]")]);
		}

		return [true, ''];
	}

	public function getAlberoUnilevel(int $idUtente, int $livello, string $mese, string $punti, int $idVista, string $locale) : array{
		try{
			$results = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallGetAlberoUnilevel($idUtente, $livello, $mese, $punti, $idVista, $locale));
			$results = json_decode($results, true);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return [true, $results['data']];
	}

	private function apiCallGetAlberoUnilevel(int $idUtente, int $livello, string $mese, string $punti, int $idVista, string $locale) : callable{
		return function(ItemInterface $item) use ($locale, $idUtente, $livello, $mese, $punti, $idVista){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/albero?gruppo_di=' . $idUtente . '&livello=' . $livello . '&mese=' . $mese . '&punti=' . $punti . '&idVista=' . $idVista . '&locale=' . $locale);

			$item->expiresAfter($this->ttlForRete);
			$item->tag($this->authenticatedCacheTag(self::TAG_ALBERI . $idVista . "[" . $locale . "]"));

			if($response->getStatusCode() != 200){
				return [false, $response->getReasonPhrase()];
			}

			return (string) $response->getBody();
		};
	}
}
