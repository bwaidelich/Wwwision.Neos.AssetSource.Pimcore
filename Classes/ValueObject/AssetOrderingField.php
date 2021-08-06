<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class AssetOrderingField
{
    private const ALLOWED_VALUES = ['modificationDate', 'filename', 'creationDate'];

    /**
     * @var self[]
     */
    private static array $instances = [];

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    private static function constant(string $value): self
    {
        return self::$instances[$value] ?? self::$instances[$value] = new self($value);
    }

    public static function fromString(string $value): self
    {
        if (!\in_array($value, self::ALLOWED_VALUES, true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not allowed. Allowed values include: "%s"', $value, implode('", "', self::ALLOWED_VALUES)), 1628064828);
        }
        return self::constant($value);
    }

    public static function MODIFICATION_DATE(): self
    {
        return self::fromString('modificationDate');
    }

    public static function FILENAME(): self
    {
        return self::fromString('filename');
    }

    public static function CREATION_DATE(): self
    {
        return self::fromString('creationDate');
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
