<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class AssetOrdering
{
    public AssetOrderingField $field;
    public bool $descending;

    private function __construct(AssetOrderingField $field, bool $descending)
    {
        $this->field = $field;
        $this->descending = $descending;
    }

    public static function by(AssetOrderingField $field): self
    {
        return new self($field, false);
    }

    public static function default(): self
    {
        return new self(AssetOrderingField::MODIFICATION_DATE(), true);
    }

    public function descending(): self
    {
        $newInstance = clone $this;
        $newInstance->descending = true;
        return $newInstance;
    }
}
