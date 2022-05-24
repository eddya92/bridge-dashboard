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
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;
use function dd;

final class RestReteRepository implements ReteRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForRete,
		private string                 $locales,
	){
	}

	/**
	 * @inheritdoc
	 * @throws JsonException|InvalidArgumentException
	 */
	public function allVisteUnilevelOfIdUtente($locale) : Generator{
		$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallGetAlberoViste($locale));
		$results = Json::decode($cached);

		foreach($results['data'] as $item){
			yield new AlberoUnilevelVistaViewModel((int) $item['id'], $item['nome'], (bool) $item['default'], (int) $item['id_utente_albero'], $item['nome_utente'], $item['livelli'], $item['mese'], $item['altezza'], $item['larghezza'], $item['orientamento'], $item['punti']);
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
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_VISTE_ALBERI . "[" . $locale . "]")]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @inheritdoc
	 * @throws GuzzleException|InvalidArgumentException
	 */
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

	/**
	 * @inheritdoc
	 * @throws InvalidArgumentException|GuzzleException
	 */
	public function salvaAlberoVista(bool $isDefault, int $ID_vista, string $Nome, int $ID_utente_albero, int $max_livelli, string $Mese, int $height, int $width, string $orientamento, string $icon, string $punti) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/utenti/vista-albero', [
				'form_params' => [
					'isDefault'        => ($isDefault ? 1 : 0),
					'ID_vista'         => $ID_vista,
					'Nome'             => $Nome,
					'ID_utente_albero' => $ID_utente_albero,
					'max_livelli'      => $max_livelli,
					'Mese'             => $Mese,
					'height'           => $height,
					'width'            => $width,
					'orientamento'     => $orientamento,
					'icon'             => $icon,
					'punti'            => $punti,
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

	/**
	 * @inheritdoc
	 */
	public function unilevelTreeOfIdUtente(int $ID_utente, string $Mese, string $punti, string $icon, bool $show_disattivi, bool $hide_nulli, string $locale) : array{
		try{
			$results = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallUnilevelTreeOfIdUtente($ID_utente, $Mese, $punti, $icon, $show_disattivi, $hide_nulli, $locale));
			$results = json_decode($results, true);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return [true, $results['data']];
	}

	private function apiCallUnilevelTreeOfIdUtente(int $ID_utente, string $Mese, string $punti, string $icon, bool $show_disattivi, bool $hide_nulli, string $locale) : callable{
		return function(ItemInterface $item) use ($ID_utente, $Mese, $punti, $icon, $show_disattivi, $hide_nulli, $locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/albero?gruppo_di=' . $ID_utente . '&mese=' . $Mese . '&punti=' . $punti . '&icon=' . $icon . '&show_disattivi=' . $show_disattivi . '&hide_nulli=' . $hide_nulli . '&locale=' . $locale);

			$item->expiresAfter($this->ttlForRete);
			$item->tag($this->authenticatedCacheTag(self::TAG_ALBERI . $ID_utente . '_' . $Mese . "[" . $locale . "]"));

			if($response->getStatusCode() != 200){
				return [false, $response->getReasonPhrase()];
			}

			return (string) $response->getBody();
		};
	}
}
