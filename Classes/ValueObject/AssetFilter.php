<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;
use Webmozart\Assert\Assert;

/**
 * @Flow\Proxy(false)
 */
final class AssetFilter
{
    public ?string $searchTerm = null;
    public AssetType $assetType;

    private function __construct()
    {
        $this->assetType = AssetType::ALL();
    }

    public static function default(): self
    {
        return new self();
    }

    public function forSearchTerm(string $searchTerm): self
    {
        Assert::stringNotEmpty($searchTerm, 'search term must not be empty');
        $newInstance = clone $this;
        $newInstance->searchTerm = $searchTerm;
        return $newInstance;
    }

    public function forAssetType(AssetType $assetType): self
    {
        $newInstance = clone $this;
        $newInstance->assetType = $assetType;
        return $newInstance;
    }

    public function toFilterArray(): array
    {
        $constraints = [];
        if ($this->assetType === AssetType::ALL()) {
            $constraints[] = ['type' => ['$not' => 'folder']];
        } else {
            $constraints[] = ['type' => $this->assetType->toString()];
        }
        if ($this->searchTerm !== null) {
            $constraints[] = ['$or' => [
                ['id' => $this->searchTerm],
                ['filename' => ['$like' => '%' . $this->searchTerm . '%']],
                ['path' => ['$like' => '%' . $this->searchTerm . '%']],
            ]];
        }
        if (\count($constraints) === 1) {
            return $constraints[0];
        }
        return ['$and' => $constraints];
    }

    public function toFilterString(): ?string
    {
        $filterArray = $this->toFilterArray();
        if ($filterArray === []) {
            return null;
        }
        try {
            return \json_encode($filterArray, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException(sprintf('Failed to JSON encode filter data: %s', $e->getMessage()), 1628265377, $e);
        }
    }

}
