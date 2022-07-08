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
use InvalidArgumentException;
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

	/**
	 * @inheritDoc
	 *
	 * @return Generator
	 * @throws InvalidArgumentException
	 */
	public function getEmailTemplates(string $locale) : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallEmail($locale));
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $result){
			yield new EmailTemplateViewModel((int) $result['id'], $result['foto'], $result['intestazione'], $result['oggetto'], $result['email'], $result['email_1h'], $result['email_1d'], $result['email_1w'], $result['email_1m'], $result['email_1h_totali'], $result['email_1d_totali'], $result['email_1w_totali'], $result['email_1m_totali']);
		}
	}

	/**
	 * @param string $_locale
	 *
	 * @return callable
	 */
	private function apiCallEmail(string $locale) : callable{
		return function(ItemInterface $item) use ($locale){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/email/template-disponibili?locale=' . $locale, ['connect_timeout' => 10.00]);

			$item->expiresAfter($this->ttlForEmailInviate);
			$item->tag($this->authenticatedCacheTag(self::TAG_EMAILS . $locale));

			//per invalidarlo
			//$this->cache->invalidateTags([$this->authenticatedCacheTag(self::TAG_EMAILS)]);

			if($response->getStatusCode() != 200){
				throw new Exception($response->getReasonPhrase());
			}

			return (string) $response->getBody();
		};
	}

	/**
	 * @param string $nome
	 * @param string $email
	 * @param int    $idEmail
	 *
	 * @return array
	 */
	public function inviaInvito(string $nome, string $email, int $idEmail) : array{
		try{
			$results = $this->apiCallInviaInvito($nome, $email, $idEmail);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		return $results;
	}

	/**
	 * @param string $nome
	 * @param string $email
	 * @param int    $idEmail
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function apiCallInviaInvito(string $nome, string $email, int $idEmail) : array{
		$response = $this->restApiConnection()
			->withAuthentication($this->authenticationToken())
			->client()
			->request('POST', '/db-v1/email/invia-invito', [
				'form_params'     => [
					'nominativo' => $nome,
					'email'      => $email,
					'id_email'   => $idEmail,
				],
				'connect_timeout' => 10.00,
			]);

		if($response->getStatusCode() != 200){
			return [false, $response->getReasonPhrase()];
		}

		return [true, ''];
	}

	/**
	 * @inheritDoc
	 *
	 * @return Generator
	 */
	public function getElencoEmailInviate() : Generator{
		try{
			$cached = $this->cache->get($this->authenticatedCacheKey(), $this->apiCallEmails());
			$results = Json::decode($cached);
		}catch(Exception $exception){
			error_log($exception->getMessage());
			throw new Exception($exception->getMessage(), $exception->getCode());
		}

		foreach($results['data'] as $item){
			yield new EmailViewModel($item['data'], $item['user'], $item['email']);
		}
	}

	/**
	 * @return callable(ItemInterface): string
	 */
	private function apiCallEmails() : callable{
		return function(ItemInterface $item){
			$response = $this->restApiConnection()
				->withAuthentication($this->authenticationToken())
				->client()
				->request('GET', '/db-v1/email/email-inviate', ['connect_timeout' => 10.00]);

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
