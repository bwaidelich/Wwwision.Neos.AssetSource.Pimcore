<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\AssetSource;

use Wwwision\Neos\AssetSource\Pimcore\ValueObject\PimcoreAsset;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\PimcoreAssetList;
use Neos\Media\Domain\Model\AssetSource\AssetProxy\AssetProxyInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxyQueryResultInterface;

final class PimcoreAssetProxyQueryResult implements AssetProxyQueryResultInterface
{
    private PimcoreAssetProxyQuery $query;
    private ?PimcoreAssetList $assetsRuntimeCache = null;

    public function __construct(PimcoreAssetProxyQuery $query)
    {
        $this->query = $query;
    }

    private function getAssets(): PimcoreAssetList
    {
        if ($this->assetsRuntimeCache === null) {
            $this->assetsRuntimeCache = $this->query->getAssetList();
        }
        return $this->assetsRuntimeCache;
    }

    public function getQuery(): PimcoreAssetProxyQuery
    {
        return clone $this->query;
    }

    public function getFirst(): ?AssetProxyInterface
    {
        $assetArray = $this->getAssets()->toArray();
        $firstAsset =  reset($assetArray);
        return $firstAsset !== false ? new PimcoreAssetProxy($this->query->getAssetSource(), $firstAsset) : null;
    }

    /**
     * @return AssetProxyInterface[]
     */
    public function toArray(): array
    {
        return array_map(fn(PimcoreAsset $pimcoreAsset) => new PimcoreAssetProxy($this->query->getAssetSource(), $pimcoreAsset), $this->getAssets()->toArray());
    }

    public function current(): ?PimcoreAssetProxy
    {
        $assets = $this->getAssets();
        $pimcoreAsset = $assets->current();
        if ($pimcoreAsset === null) {
            return null;
        }
        return new PimcoreAssetProxy($this->query->getAssetSource(), $pimcoreAsset);
    }

    public function next(): void
    {
        $this->getAssets()->next();
    }

    public function key(): int
    {
        return $this->getAssets()->key();
    }

    public function valid(): bool
    {
        return $this->getAssets()->valid();
    }

    public function rewind(): void
    {
        $this->getAssets()->rewind();
    }

    public function offsetExists($offset): void
    {
        $this->getAssets()->offsetExists($offset);
    }

    public function offsetGet($offset): ?PimcoreAssetProxy

    {
        $pimcoreAsset = $this->getAssets()->offsetGet($offset);
        return $pimcoreAsset !== null ? new PimcoreAssetProxy($this->query->getAssetSource(), $pimcoreAsset) : null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->getAssets()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->getAssets()->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->getAssets()->count();
    }
}
