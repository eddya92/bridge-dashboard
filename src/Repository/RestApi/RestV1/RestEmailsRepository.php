<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\EmailsRepository;
use App\Repository\RestApi\AuthenticatedConnectionCapability;
use App\Repository\RestApi\AuthenticatedRepository;
use App\Service\Json;
use App\ViewModel\EmailTemplateViewModel;
use App\ViewModel\EmailViewModel;
use Exception;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final class RestEmailsRepository implements EmailsRepository, AuthenticatedRepository{
	use AuthenticatedConnectionCapability;

	public function __construct(
		private TagAwareCacheInterface $cache,
		private int                    $ttlForEmailInviate,
		private string                 $locales,
	){
	}

	public function getEmailTemplates(string $_locale) : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallEmail($_locale));
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $result){
			yield new EmailTemplateViewModel((int) $result['id'], $result['foto'], $result['intestazione'], $result['oggetto'], $result['email'], $result['email_1h'], $result['email_1d'], $result['email_1w'], $result['email_1m'], $result['email_1h_totali'], $result['email_1d_totali'], $result['email_1w_totali'], $result['email_1m_totali']);
		}
	}

	public function apiCallEmail(string $_locale) : callable{
		return function(ItemInterface $item) use ($_locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/email/template-disponibili');

			$item->expiresAfter($this->ttlForEmailInviate);
			$item->tag($this->authenticatedCacheTag(self::TAG_EMAILS));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_EMAILS)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	public function inviaInvito(string $nome, string $email, int $idEmail) : array{
		try{
			$results = $this->apiCallInviaInvito($nome, $email, $idEmail);
		}catch(Throwable $exception){
			return [false, $exception->getMessage()];
		}

		return $results;
	}

	private function apiCallInviaInvito(string $nome, string $email, int $idEmail) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/email/invia-invito', [
				'form_params' => [
					'nominativo' => $nome,
					'email'      => $email,
					'id_email'   => $idEmail,
				],
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	public function getElencoEmailInviate() : ?Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallEmails());
			$results = Json::decode($cached);
		}catch(Throwable){
			return null;
		}

		foreach($results['data'] as $item){
			yield new EmailViewModel($item['data'], $item['user'], $item['email']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	public function apiCallEmails() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/email/email-inviate');

			$item->expiresAfter($this->ttlForEmailInviate);
			$item->tag($this->authenticatedCacheTag(self::TAG_EMAILS));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_EMAILS)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}
}
