<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\AssetSource;

use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetFilter;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetOrdering;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetOrderingField;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetOrderings;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetType;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\Pagination;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\PimcoreAssetList;
use Neos\Media\Domain\Model\AssetSource\AssetProxyQueryInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxyQueryResultInterface;
use Neos\Media\Domain\Model\AssetSource\SupportsSortingInterface;

final class PimcoreAssetProxyQuery implements AssetProxyQueryInterface
{
    private PimcoreAssetSource $assetSource;
    private string $searchTerm = '';
    private string $assetTypeFilter = 'All';
    private array $orderings = [];
    private int $offset = 0;
    private int $limit = 30;

    private ?PimcoreAssetList $assetList = null;

    public function __construct(PimcoreAssetSource $assetSource)
    {
        $this->assetSource = $assetSource;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
        $this->assetList = null;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
        $this->assetList = null;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setSearchTerm(string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
        $this->assetList = null;
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function setAssetTypeFilter(string $assetTypeFilter): void
    {
        $this->assetTypeFilter = $assetTypeFilter;
        $this->assetList = null;
    }

    public function setOrderings(array $orderings): void
    {
        $this->orderings = $orderings;
        $this->assetList = null;
    }

    public function execute(): AssetProxyQueryResultInterface
    {
        return new PimcoreAssetProxyQueryResult($this);
    }

    public function count(): int
    {
        return $this->getAssetList()->count();
    }

    public function getAssetSource(): PimcoreAssetSource
    {
        return $this->assetSource;
    }

    public function getAssetList(): PimcoreAssetList
    {
        if ($this->assetList === null) {
            $filter = AssetFilter::default();
            if ($this->assetTypeFilter !== 'All') {
                $filter = $filter->forAssetType(AssetType::fromString(strtolower($this->assetTypeFilter)));
            }
            if ($this->searchTerm !== '') {
                $filter = $filter->forSearchTerm($this->searchTerm);
            }
            $orderings = [];
            foreach ($this->orderings as $neosOrderingField => $direction) {
                if ($neosOrderingField === 'resource.filename') {
                    $ordering = AssetOrdering::by(AssetOrderingField::FILENAME());
                } else {
                    $ordering = AssetOrdering::by(AssetOrderingField::MODIFICATION_DATE());
                }
                if ($direction === SupportsSortingInterface::ORDER_DESCENDING) {
                    $ordering = $ordering->descending();
                }
                $orderings[] = $ordering;
            }
            $this->assetList = $this->assetSource->getPimcoreClient()->getAssets($filter, AssetOrderings::fromArray($orderings), Pagination::forOffsetAndLimit($this->offset, $this->limit));
        }
        return $this->assetList;
    }
}
