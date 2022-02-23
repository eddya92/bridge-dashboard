<?php
declare(strict_types=1);

namespace App\Repository\InMemory;

use App\Repository\OneRepository;
use App\Repository\Top5Repository;
use App\Service\CacheKey;
use App\Service\Json;
use App\ViewModel\Top5UserViewModel;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use function random_int;

final class InMemoryTop5Repository
{
    public function __construct(private TagAwareCacheInterface $cache)
    {
    }

    /**
     * @inheritdoc
     */
    public function get(string $userId, string $oneParam, string $anotherParam): string
    {
        return $this->cache->get(
            CacheKey::fromTrace(),
            static function (ItemInterface $item) use ($userId, $oneParam, $anotherParam) {
                // Do something with $userId, $oneParam and $anotherParam which gives some $result
                $result = 'value from remote';

                $item->expiresAfter(10);

                return $result;
            }
        );
    }

    /**
     * @return callable(Generator<int, string>)
     */
    private function callableList(): callable
    {
        return static function (ItemInterface $item) : string {
            // Response from REST API called with $userId and $firstParam which gives some $result
            $result = "http://localhost:8081/users";

            $item->expiresAfter(30);

            return $result;
        };
    }

    /**
     * @inheritdoc
     */
    public function listOfTop5(): Generator
    {
        $cached = $this->cache->get(CacheKey::fromTrace(), $this->callableList());
        $results = Json::decode($cached);

        foreach ($results['data'] as $item) {
            yield $item['name'];
        }
    }



    public function listOfTop5Ajax($year,$month): Generator
    {
        // TODO: Implement listOfTop5Ajax() method.
    }

	public function Top5Ajax($year, $month) : Generator{
		// TODO: Implement Top5Ajax() method.
	}
}
