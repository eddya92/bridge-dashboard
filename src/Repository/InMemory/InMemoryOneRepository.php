<?php
declare(strict_types=1);

namespace App\Repository\InMemory;

use App\Repository\OneRepository;
use App\Service\CacheKey;
use App\Service\Json;
use App\ViewModel\Top5UserViewModel;
use Generator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use function random_int;

final class InMemoryOneRepository implements OneRepository
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
     * @inheritdoc
     */
    public function listOfSomething(string $userId, string $firstParm): Generator
    {
        $cached = $this->cache->get(CacheKey::fromTrace(), $this->callableList($userId, $firstParm));
        $results = Json::decode($cached);

        foreach ($results['data'] as $item) {
            yield $item['name'];
        }
    }

    /**
     * @inheritdoc
     */
    public function listOfUsers(string $userId, string $firstParm): Generator
    {
        $cached = $this->cache->get(CacheKey::fromTrace(), $this->callableList($userId, $firstParm));
        $results = Json::decode($cached);

        foreach ($results['data'] as $item) {
            yield $item['name'];
        }
    }
    /**
     * @inheritdoc
     */
    public function listOfAnotherThing(string $param): Generator
    {
        yield new Top5UserViewModel('Mario', random_int(1, 100));
        yield new Top5UserViewModel('Carlo', random_int(1, 100));
    }

    /**
     * @inheritdoc
     */
    public function listOfCombinedThings(string $param): Generator
    {
        yield new Top5UserViewModel('Mario', random_int(1, 100));
        yield new Top5UserViewModel('Carlo', random_int(1, 100));
    }

    /**
     * @return callable(Generator<int, string>)
     */
    private function callableList(string $userId, string $firstParm): callable
    {
        return static function (ItemInterface $item) use ($userId, $firstParm): string {
            // Response from REST API called with $userId and $firstParam which gives some $result
            $result = <<<JSON
            {
                "data":
                [
                    {
                        "name": "first item"
                    },
                    {
                        "name": "second item"
                    },
                    {
                        "name": "third item"
                    },
                    {
                        "name": "fourth item"
                    }
                ]
            }            
            JSON;

            $item->expiresAfter(30);

            return $result;
        };
    }

}
