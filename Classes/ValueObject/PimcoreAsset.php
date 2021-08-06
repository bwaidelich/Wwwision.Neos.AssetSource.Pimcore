<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\UriInterface;

/**
 * @Flow\Proxy(false)
 */
final class PimcoreAsset
{
    public int $id;
    public string $filename;
    public int $filesize;
    public UriInterface $url;
    public ?UriInterface $thumbnailUrl;
    public ?UriInterface $previewUrl;
    public string $mimetype;
    public array $metadata;
    public \DateTimeImmutable $creationDate;
    public \DateTimeImmutable $modificationDate;

    private function __construct(int $id, string $filename, int $filesize, UriInterface $url, ?UriInterface $thumbnailUrl, ?UriInterface $previewUrl, string $mimetype, array $metadata, \DateTimeImmutable $creationDate, \DateTimeImmutable $modificationDate)
    {
        $this->id = $id;
        $this->filename = $filename;
        $this->filesize = $filesize;
        $this->url = $url;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->previewUrl = $previewUrl;
        $this->mimetype = $mimetype;
        $this->metadata = $metadata;
        $this->creationDate = $creationDate;
        $this->modificationDate = $modificationDate;
    }

    public static function fromApiResult(array $result, UriInterface $baseUrl): self
    {
        $url = $baseUrl->withPath($result['fullpath']);
        $thumbnailUrl = $result['fullpath_thumbnail'] !== null ? $baseUrl->withPath($result['fullpath_thumbnail']) : null;
        $previewUrl = $result['fullpath_preview'] !== null ? $baseUrl->withPath($result['fullpath_preview']) : null;
        $metadata = [];
        if ($result['metadata'] !== null) {
            foreach ($result['metadata'] as $metadatum) {
                $metadata[$metadatum['name']] = $metadatum['data'];
            }
        }
        return new self(
            (int)$result['id'],
            $result['filename'],
            $result['filesize'],
            $url,
            $thumbnailUrl,
            $previewUrl,
            $result['mimetype'],
            $metadata,
            \DateTimeImmutable::createFromFormat('U', (string)$result['creationDate']),
            \DateTimeImmutable::createFromFormat('U', (string)$result['modificationDate']),
        );
    }

}
