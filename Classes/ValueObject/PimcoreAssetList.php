<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\UriInterface;

/**
 * @Flow\Proxy(false)
 */
final class PimcoreAssetList implements \Iterator, \ArrayAccess, \Countable
{
    private int $totalCount;
    /**
     * @var array<PimcoreAsset>
     */
    private array $assets;

    private int $pos = 0;

    private function __construct(int $totalCount, array $assets)
    {
        $this->totalCount = $totalCount;
        $this->assets = $assets;
    }

    public static function fromApiResult(array $result, UriInterface $baseUrl): self
    {
        $assets = array_map(static fn(array $edge) => PimcoreAsset::fromApiResult($edge['node'], $baseUrl), $result['edges']);
        return new self($result['totalCount'], array_values($assets));
    }

    public function count(): int
    {
        return $this->totalCount;
    }

    public function current(): ?PimcoreAsset
    {
        return $this->assets[$this->pos];
    }

    public function next(): void
    {
        $this->pos ++;
    }

    public function key(): int
    {
        return $this->pos;
    }

    public function valid(): bool
    {
        return isset($this->assets[$this->pos]);
    }

    public function rewind(): void
    {
        $this->pos = 0;
    }

    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->assets);
    }

    public function offsetGet($offset): ?PimcoreAsset
    {
        return $this->assets[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException('This object is immutable');
    }

    public function offsetUnset($offset): void
    {
        throw new \BadMethodCallException('This object is immutable');
    }

    /**
     * @return array<PimcoreAsset>
     */
    public function toArray(): array
    {
        return array_values($this->assets);
    }
}
