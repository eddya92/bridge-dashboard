<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\UtentiStrutturaRepository;
use App\Service\Json;
use App\ViewModel\UtenteStrutturaViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestUtentiStrutturaRepository implements UtentiStrutturaRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForUtentiStruttura
	){
	}

	public function getUtentiStruttura(string $filtroGruppoDi, string $filtroNominativo, string $filtroEmail, string $filtroCellulare, string $filtroPeriodo, string $filtroDiretti, string $filtroColonnaOrdinamento, string $filtroDirezioneOrdinamento, string $tipologiaUtenza, string $items, string $pag) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallstrutturaPersonale($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $tipologiaUtenza, $items, $pag));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new UtenteStrutturaViewModel($item['id'], $item['codice'], $item['nominativo'], $item['email'], $item['cellulare'], $item['livello'], $item['qualifica'], $item['colore'], $item['sponsor']);
		}
	}

	private function apiCallstrutturaPersonale(string $filtroGruppoDi, string $filtroNominativo, string $filtroEmail, string $filtroCellulare, string $filtroPeriodo, string $filtroDiretti, string $filtroColonnaOrdinamento, string $filtroDirezioneOrdinamento, string $tipologiaUtenza, string $items, string $pag){
		return function(ItemInterface $item) use ($filtroGruppoDi, $filtroNominativo, $filtroEmail, $filtroCellulare, $filtroPeriodo, $filtroDiretti, $filtroColonnaOrdinamento, $filtroDirezioneOrdinamento, $tipologiaUtenza, $items, $pag){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/struttura' . '?gruppo_di=' . $filtroGruppoDi . '&nominativo=' . $filtroNominativo . '&email=' . $filtroEmail . '&cellulare=' . $filtroCellulare . '&periodo=' . $filtroPeriodo . '&solo_diretti=' . $filtroDiretti . '&ordinamento=' . $filtroColonnaOrdinamento . '&direzione=' . $filtroDirezioneOrdinamento . '&items=' . $items . '&tipologia_utenza=' . $tipologiaUtenza . '&pag=' . $pag);
			$item->expiresAfter($this->ttlForUtentiStruttura);
			$item->tag($this->authenticatedCacheTag(self::TAG_UTENTI_STRUTTURA));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_UTENTI_STRUTTURA)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
