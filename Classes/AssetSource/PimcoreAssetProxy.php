<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\AssetSource;

use Wwwision\Neos\AssetSource\Pimcore\ValueObject\PimcoreAsset;
use Neos\Media\Domain\Model\AssetSource\AssetProxy\AssetProxyInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxy\HasRemoteOriginalInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxy\SupportsIptcMetadataInterface;
use Psr\Http\Message\UriInterface;

final class PimcoreAssetProxy implements AssetProxyInterface, HasRemoteOriginalInterface, SupportsIptcMetadataInterface
{
    private PimcoreAssetSource $assetSource;
    private PimcoreAsset $pimcoreAsset;
    private ?string $localAssetIdentifier = null;

    public function __construct(PimcoreAssetSource $assetSource, PimcoreAsset $pimcoreAsset)
    {
        $this->assetSource = $assetSource;
        $this->pimcoreAsset = $pimcoreAsset;
    }

    public function getAssetSource(): PimcoreAssetSource
    {
        return $this->assetSource;
    }

    public function getIdentifier(): string
    {
        return (string)$this->pimcoreAsset->id;
    }

    public function getLabel(): string
    {
        return $this->pimcoreAsset->filename;
    }

    public function getFilename(): string
    {
        return $this->pimcoreAsset->filename;
    }

    public function getLastModified(): \DateTimeInterface
    {
        return $this->pimcoreAsset->modificationDate;
    }

    public function getFileSize(): int
    {
        return $this->pimcoreAsset->filesize;
    }

    public function getMediaType(): string
    {
        return $this->pimcoreAsset->mimetype;
    }

    public function hasIptcProperty(string $propertyName): bool
    {
        return isset($this->pimcoreAsset->metadata[$propertyName]);
    }

    public function getIptcProperty(string $propertyName): string
    {
        return $this->pimcoreAsset->metadata[$propertyName] ?? '';
    }

    public function getIptcProperties(): array
    {
        return $this->pimcoreAsset->metadata;
    }

    public function getWidthInPixels(): ?int
    {
        // TODO implement
        return null;
    }

    public function getHeightInPixels(): ?int
    {
        // TODO implement
        return null;
    }

    /**
     * @return UriInterface
     */
    public function getThumbnailUri(): ?UriInterface
    {
        return $this->pimcoreAsset->thumbnailUrl;
    }

    public function getPreviewUri(): ?UriInterface
    {
        return $this->pimcoreAsset->previewUrl;
    }

    public function getOriginalUri(): ?UriInterface
    {
        return $this->pimcoreAsset->url;
    }

    /**
     * @return false|resource
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getImportStream()
    {
        // TODO: This is currently required because the SSL certificate of the Pimcore API is invalid/self-signed
        $context = stream_context_create(['ssl' => ['verify_peer' => false]]);
        return fopen((string)$this->pimcoreAsset->url, 'rb', false, $context);
    }

    public function getLocalAssetIdentifier(): ?string
    {
        if ($this->localAssetIdentifier === null) {
            $this->localAssetIdentifier = $this->assetSource->getLocalAssetIdentifier($this->getIdentifier());
        }
        return $this->localAssetIdentifier;
    }

    public function isImported(): bool
    {
        return $this->getLocalAssetIdentifier() !== null;
    }
}
