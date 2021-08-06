<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\AssetSource;

use GuzzleHttp\Psr7\Uri;
use Wwwision\Neos\AssetSource\Pimcore\Service\PimcoreClient;
use Neos\Flow\Annotations\Proxy;
use Neos\Media\Domain\Model\AssetSource\AssetSourceInterface;
use Neos\Media\Domain\Model\ImportedAsset;
use Neos\Media\Domain\Repository\ImportedAssetRepository;

/**
 * @Proxy(false)
 */
final class PimcoreAssetSource implements AssetSourceInterface
{
    private string $id;
    private string $label;
    private string $description;
    private PimcoreClient $client;
    private ImportedAssetRepository $importedAssetRepository;

    public function __construct(string $id, array $options)
    {
        if (preg_match('/^[a-z][a-z0-9-]{0,62}[a-z]$/', $id) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid asset source identifier "%s". The identifier must match /^[a-z][a-z0-9-]{0,62}[a-z]$/', $id), 1627991223);
        }
        $this->id = $id;
        $this->label = $options['label'] ?? 'Pimcore';
        $this->description = $options['description'] ?? '';
        $apiOptions = $options['api'];
        $this->client = new PimcoreClient(new Uri($apiOptions['baseUrl']), $apiOptions['endpoint'], $apiOptions['apiKey'], $apiOptions['additionalConfiguration'] ?? []);
        $this->importedAssetRepository = new ImportedAssetRepository();
    }

    public static function createFromConfiguration(string $assetSourceIdentifier, array $assetSourceOptions): self
    {
        return new self($assetSourceIdentifier, $assetSourceOptions);
    }

    public function getIdentifier(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAssetProxyRepository(): PimcoreAssetProxyRepository
    {
        return new PimcoreAssetProxyRepository($this);
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function getIconUri(): string
    {
        return 'https://pimcore.com/safari-pinned-tab.svg';
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPimcoreClient(): PimcoreClient
    {
        return $this->client;
    }

    public function getLocalAssetIdentifier(string $remoteAssetIdentifier): ?string
    {
        $importedAsset = $this->importedAssetRepository->findOneByAssetSourceIdentifierAndRemoteAssetIdentifier($this->getIdentifier(), $remoteAssetIdentifier);
        return $importedAsset instanceof ImportedAsset ? $importedAsset->getLocalAssetIdentifier() : null;
    }
}
