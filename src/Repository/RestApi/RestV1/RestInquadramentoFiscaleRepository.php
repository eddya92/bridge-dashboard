<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\InquadramentoFiscaleRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\InquadramentoFiscaleViewModel;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestInquadramentoFiscaleRepository implements InquadramentoFiscaleRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForInquadramentoFiscale,
		private string                 $locales,
		private LoggerInterface        $logger
	){
	}

	public function getInquadramentoFiscale(string $_locale) : ?InquadramentoFiscaleViewModel{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallInquadramentoFiscale($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		$item = $results['data'];

		return new InquadramentoFiscaleViewModel($item['nome'], $item['descrizione'], $item['email_azienda'], $item['iban'], $item['bank_code']);
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallInquadramentoFiscale(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/utenti/inquadramento-fiscale');

			$item->expiresAfter($this->ttlForInquadramentoFiscale);
			$item->tag($this->authenticatedCacheTag(self::TAG_INQUADRAMENTO_FISCALE . "[" . $_locale . "]"));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INQUADRAMENTO_FISCALE)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function aggiornaPagamenti(string $iban, string $bankCode) : array{
		try{
			$results = $this->apiCallAggiornaPagamenti($iban, $bankCode);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallAggiornaPagamenti(string $iban, string $bankCode) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('PUT', '/db-v1/utenti/dati-pagamento', [
				'form_params' => [
					'iban'     => $iban,
					'bankCode' => $bankCode,
				],
			]);
		$this->logger->info('CHIAMATA PUT AGGIORNA PAGAMENTI ', ['PUT', '/db-v1/utenti/dati-pagamento' . 'form_params' => [
			'iban'     => $iban,
			'bankCode' => $bankCode,
		], [$response->getBody()]]);

		$locales = explode(',', $this->locales);
		foreach($locales as $locale){
			$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_INQUADRAMENTO_FISCALE . "[" . $locale . "]")]);
		}

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}
}
