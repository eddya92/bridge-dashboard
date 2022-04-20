<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\OrdiniRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\DettaglioOrdineViewModel;
use App\ViewModel\OrdiniViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestOrdiniRepository implements OrdiniRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForOrdini = 1
	){
	}

	public function getOrdini(string $sottoposti, string $clienti, string $esito, string $data_dal, string $data_al, string $tipolgia_ordine, string $colonna, string $ordinamento, string $items, string $pag) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallOrdini($sottoposti, $clienti, $esito, $data_dal, $data_al, $tipolgia_ordine, $colonna, $ordinamento, $items, $pag));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new OrdiniViewModel($item['id'], $item['data_ordine'], $item['codice_ordine'], $item['user'], $item['pc'], $item['cv'] = 0.0, $item['totale'], $item['esito'], $item['esito_colore'], $item['visibile'], $item['tipologia_ordine'], $results['metadata']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallOrdini(string $sottoposti, string $clienti, string $esito, string $data_dal, string $data_al, string $tipolgia_ordine, string $colonna, string $ordinamento, string $items, string $pag) : callable{
		return function(ItemInterface $item) use ($sottoposti, $clienti, $esito, $data_dal, $data_al, $tipolgia_ordine, $colonna, $ordinamento, $items, $pag) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/ordini/ordine' . '?sottoposti=' . $sottoposti . '&id_utente=' . $clienti . '&id_esito=' . $esito . '&data_inizio=' . $data_dal . '&data_fine=' . $data_al . '&tipi_ordine=' . $tipolgia_ordine . '&campo_ordine=' . $colonna . '&direzione_ordine=' . $ordinamento . '&items=' . $items . '&pag=' . $pag);
			//, [
			//					'id_utente'   => $clienti,
			//					'sottoposti'  => $sottoposti,
			//					'id_esito'    => $esito,
			//					'tipi_ordine' => $tipolgia_ordine,
			//					'data_inizio' => $data_dal,
			//					'data_fine'   => $data_al,
			//				]
			///ordini-api?sottoposti=Antonio%20De%20Carolis%2000006&ricerca_clienti=Antonio%20De%20Carolis%2000006&iD_esito=%7CPagato%20e%20spedito-2%7C%7CIn%20lavorazione-3%7C&data_contratto_inizio=10%2F11%2F2021&data_contratto_fine=11%2F11%2F2021&tipologia_ordine=%7CConsumo%20personale-1%7C%7CVendite%20promosse%20a%20clienti-2%7C&_=1636557747633
			//$item->expiresAfter($this->ttlForOrdini);
			//$item->tag($this->authenticatedCacheTag(self::TAG_ORDINI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ORDINI)]);

			//$this->logger->log('GET', '/db-v1/ordini/ordine' . '?sottoposti=' . $sottoposti . '&id_utente=' . $clienti . '&id_esito=' . $esito . '&data_inizio=' . $data_dal . '&data_fine=' . $data_al . '&tipi_ordine=' . $tipolgia_ordine . '&campo_ordine=' . $colonna . '&direzione_ordine=' . $ordinamento . '&items=' . $items . '&pag=' . $pag);
			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function getDettaglioOrdine(int $id) : ?DettaglioOrdineViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallDettaglioOrdine($id));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$results = $results['data'];

		return new DettaglioOrdineViewModel($results['id'], $results['codice'], $results['azienda'], $results['data'], $results['esito'], $results['sponsor'], $results['intestatario'], $results['spedizione'], $results['articoli'], $results['modalita_pagamento'], $results['modalita_spedizione'], $results['spese_amministrative'], $results['totale'], $results['punti'], $results['note'], $results['pagamento']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallDettaglioOrdine(int $id) : callable{
		return function(ItemInterface $item) use ($id) : string{
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/ordini/dettaglio-ordine/' . $id);

			//	$item->expiresAfter($this->ttlForOrdini);
			//	$item->tag($this->authenticatedCacheTag(self::TAG_ORDINI));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_ORDINI)]);

			if($response->getStatusCode() != 200){
				return $response->getReasonPhrase();
			}

			return (string) $response->getBody();
		};
	}
}


