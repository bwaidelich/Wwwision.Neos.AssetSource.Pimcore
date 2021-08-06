<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class AssetType
{
    private const ALLOWED_VALUES = [
        '__all__',
        'image',
        'document',
        'video',
        'audio',
    ];

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
            throw new \InvalidArgumentException(sprintf('"%s" is not allowed. Allowed values include: "%s"', $value, implode('", "', self::ALLOWED_VALUES)), 1628064419);
        }
        return self::constant($value);
    }

    public static function ALL(): self
    {
        return self::fromString('__all__');
    }

    public static function IMAGE(): self
    {
        return self::fromString('image');
    }

    public static function DOCUMENT(): self
    {
        return self::fromString('document');
    }

    public static function VIDEO(): self
    {
        return self::fromString('video');
    }

    public static function AUDIO(): self
    {
        return self::fromString('audio');
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function __clone()
    {
        throw new \RuntimeException('Cloning not supported');
    }

    public function __sleep(): array
    {
        throw new \RuntimeException('Serialization not supported');
    }

    public function __wakeup(): void
    {
        throw new \RuntimeException('Deserialization not supported');
    }
}
