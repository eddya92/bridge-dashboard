<?php
declare(strict_types=1);

namespace App\Repository\RestApi\RestV1;

use App\Repository\RestApi\AppKey;
use App\Repository\RestApi\RestApiConnection;
use App\Repository\RestApi\Token;
use GuzzleHttp\Client;
use function array_merge;
use function sprintf;

final class RestApiV1Connection implements RestApiConnection{
	public const BRIDGEXL_TOKEN = 'X-BridgeXL-Token';
	private Client $client;

	private function __construct(private array $config, private AppKey $appKey){
		$config['headers'] = array_merge($config['headers'] ?? [], ['User-Agent' => $appKey->toString()]);
		$this->client = new Client($config);
	}

	public static function create(array $config, AppKey $appKey) : self{
		return new self($config, $appKey);
	}

	public function withAuthentication(Token $token) : self{
		$headers = array_merge($this->config['headers'] ?? [], [self::BRIDGEXL_TOKEN => $token->toString()]);

		return self::create(array_merge($this->config, ['headers' => $headers]), $this->appKey);
	}

	public function client() : Client{
		return $this->client;
	}

	public function signature() : string{
		return sprintf('%s|%s', self::class, $this->appKey->toString());
	}
}
