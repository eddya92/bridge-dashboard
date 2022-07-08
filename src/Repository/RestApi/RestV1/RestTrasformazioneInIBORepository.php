<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Repository\TrasformazioneInIBORepository;
use Exception;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class RestTrasformazioneInIBORepository implements TrasformazioneInIBORepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,

	){
	}

	public function trasformaInIbo(
		string $codice,
		string $codice_fiscale,
		string $piva,
		string $pec,
		string $codice_univoco,
		string $indirizzo,
		string $numeroCivico,
		string $cap,
		string $comune,
		string $provincia,
		string $telefono,
		string $cellulare) : array{
		try{
			$results = $this->apiCallTrasformaInIbo(
				$codice,
				$codice_fiscale,
				$piva,
				$pec,
				$codice_univoco,
				$indirizzo,
				$numeroCivico,
				$cap,
				$comune,
				$provincia,
				$telefono,
				$cellulare

			);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	private function apiCallTrasformaInIbo(string $codice, string $codice_fiscale, string $piva, string $pec, string $codice_univoco, string $indirizzo, string $numeroCivico, string $cap, string $comune, string $provincia, string $telefono, string $cellulare){
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request(('POST'), '/db-v1/utenti/trasformazione-incaricato', [
				'form_params'     => [
					'codice'             => $codice,
					'codiceFiscale'      => $codice_fiscale,
					'partitaIva'         => $piva,
					'pec'                => $pec,
					'codiceUnivoco'      => $codice_univoco,
					'indirizzoResidenza' => $indirizzo,
					'civicoResidenza'    => $numeroCivico,
					'capResidenza'       => $cap,
					'provinciaResidenza' => $comune,
					'telefono'           => $provincia,
					'note'               => $telefono,
					'cellulare'          => $cellulare,
				],
				'connect_timeout' => 10.00,
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INDIRIZZI)]);
		return [true, ''];
	}
}
