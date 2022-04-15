<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\CarrelloRepository;
use App\ViewModel\CarrelloTotaliViewModel;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\CarrelloViewModel;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestCarrelloRepository implements CarrelloRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForCarrello,
		private string                 $locales,
	){
	}

	public function getCarrello(string $_locale) : ?CarrelloViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallCarrello($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$results = $results['data'];

		return new CarrelloViewModel($results['id'], $results['totale'], $results['punti'], $results['articoli'], $results['valuta'], $results['quantita_totale']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallCarrello(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carrelli/carrello');

			$item->expiresAfter($this->ttlForCarrello);
			$item->tag($this->authenticatedCacheTag(self::TAG_CARRELLO . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRELLO)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function apriNuovoCarrello(string $store, string $listino, string $id_tipo_ordine) : array{
		try{
			$results = $this->apiCallApriNuovoCarrello($store, $listino, $id_tipo_ordine);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallApriNuovoCarrello(string $store, string $listino, string $id_tipo_ordine) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/carrelli/carrello', [
				'form_params' => [
					'id_store'       => $store,
					'codice_listino' => $listino,
					'id_tipo_ordine' => $id_tipo_ordine,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	public function aggiungiArticolo(int $ID_articolo, int $ID_variante = 0, int $quantita = 0) : array{
		try{
			$results = $this->apiCallAggiungiArticolo($ID_articolo, $ID_variante, $quantita);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallAggiungiArticolo(int $ID_articolo, int $ID_variante = 0, int $quantita = 0) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/carrelli/articolo', [
				'form_params' => [
					'id_articolo' => $ID_articolo,
					'id_variante' => $ID_variante,
					'quantita'    => $quantita,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRELLO)]);

		return [true, ''];
	}

	public function eliminaArticolo(int $ID_articolo, int $ID_variante = 0) : array{
		try{
			$results = $this->apiCallEliminaArticolo($ID_articolo, $ID_variante);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallEliminaArticolo(int $ID_articolo, int $ID_variante = 0) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('DELETE', '/db-v1/carrelli/articolo?id_articolo=' . $ID_articolo . '&id_variante=' . $ID_variante);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRELLO)]);

		return [true, ''];
	}

	public function aggiornaArticolo(int $ID_articolo, int $ID_variante = 0, int $quantita = 0) : array{
		try{
			$results = $this->apiCallAggiornaArticolo($ID_articolo, $ID_variante, $quantita);
		}catch(Exception $exception){
			return [false, json_decode($exception->getResponse()->getBody()->getContents(), true)['error_msg']];
		}

		return $results;
	}

	private function apiCallAggiornaArticolo(int $ID_articolo, int $ID_variante = 0, int $quantita = 0) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('PUT', '/db-v1/carrelli/articolo', [
				'form_params' => [
					'id_articolo' => $ID_articolo,
					'id_variante' => $ID_variante,
					'quantita'    => $quantita,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRELLO)]);

		return [true, ''];
	}

	public function getTotali(string $_locale, int $id_spedizione = 0, int $id_modsped = 0, int $id_modpag = 0) : ?CarrelloTotaliViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallTotali($_locale, $id_spedizione, $id_modsped, $id_modpag));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		return new CarrelloTotaliViewModel($results['data']['carrello'], $results['data']['spese_amministrative'], $results['data']['spese_spedizione'], $results['data']['spese_pagamento'], $results['data']['totale']);
	}

	private function apiCallTotali(string $_locale, int $id_spedizione = 0, int $id_modsped = 0, int $id_modpag = 0) : callable{
		return function(ItemInterface $item) use ($_locale, $id_spedizione, $id_modsped, $id_modpag){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/carrelli/totali?id_spedizione=' . $id_spedizione . '&id_modsped=' . $id_modsped . '&id_modpag=' . $id_modpag);

			$item->expiresAfter($this->ttlForCarrello);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function checkout(array $parametri) : array{
		try{
			$results = $this->apiCallCheckout($parametri);
		}catch(Throwable $exception){
			return [false, $exception->getMessage(), []];
		}

		return $results;
	}

	private function apiCallCheckout(array $parametri) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/ordini/ordine', [
				'form_params' => $parametri,
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase(), []];
		}

		$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_CARRELLO)]);

		return [true, '', Json::decode($response->getBody()->getContents())];
	}
}
