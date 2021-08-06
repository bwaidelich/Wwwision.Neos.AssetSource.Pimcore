<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\AssetSource;

use Neos\Media\Domain\Model\AssetSource\AssetProxy\AssetProxyInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxyQueryResultInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxyRepositoryInterface;
use Neos\Media\Domain\Model\AssetSource\AssetTypeFilter;
use Neos\Media\Domain\Model\AssetSource\SupportsSortingInterface;
use Neos\Media\Domain\Model\Tag;
use Webmozart\Assert\Assert;

final class PimcoreAssetProxyRepository implements AssetProxyRepositoryInterface, SupportsSortingInterface
{

    private PimcoreAssetSource $assetSource;
    private string $assetTypeFilter = 'All';
    private array $orderings = [];

    public function __construct(PimcoreAssetSource $assetSource)
    {
        $this->assetSource = $assetSource;
    }

    public function getAssetProxy(string $identifier): AssetProxyInterface
    {
        Assert::numeric($identifier, 'Pimcore assets must be identified by a numeric id, given: %s');
        $pimcoreAsset = $this->assetSource->getPimcoreClient()->getAsset((int)$identifier);
        return new PimcoreAssetProxy($this->assetSource, $pimcoreAsset);
    }

    public function filterByType(AssetTypeFilter $assetType = null): void
    {
        $this->assetTypeFilter = (string)$assetType ?: 'All';
    }

    public function findAll(): PimcoreAssetProxyQueryResult
    {
        $query = new PimcoreAssetProxyQuery($this->assetSource);
        $query->setAssetTypeFilter($this->assetTypeFilter);
        $query->setOrderings($this->orderings);

        return new PimcoreAssetProxyQueryResult($query);
    }

    public function findBySearchTerm(string $searchTerm): PimcoreAssetProxyQueryResult
    {
        $query = new PimcoreAssetProxyQuery($this->assetSource);
        $query->setSearchTerm($searchTerm);
        $query->setAssetTypeFilter($this->assetTypeFilter);
        $query->setOrderings($this->orderings);

        return new PimcoreAssetProxyQueryResult($query);
    }

    public function findByTag(Tag $tag): AssetProxyQueryResultInterface
    {
        throw new \BadMethodCallException('findByTag is not supported by this repository', 1628096038);
    }

    public function findUntagged(): AssetProxyQueryResultInterface
    {
        throw new \BadMethodCallException('findUntagged is not supported by this repository', 1628096038);
    }

    public function countAll(): int
    {
        $query = new PimcoreAssetProxyQuery($this->assetSource);

        return $query->count();
    }

    /**
     * Sets the property names to order results by. Expected like this:
     * array(
     *  'filename' => SupportsSortingInterface::ORDER_ASCENDING,
     *  'lastModified' => SupportsSortingInterface::ORDER_DESCENDING
     * )
     */
    public function orderBy(array $orderings): void
    {
        $this->orderings = $orderings;
    }
}
