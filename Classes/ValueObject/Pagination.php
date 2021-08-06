<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class Pagination
{
    private const DEFAULT_LIMIT = 30;

    public int $limit;
    public int $offset;

    private function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public static function default(): self
    {
        return new self(0, self::DEFAULT_LIMIT);
    }

    public static function forOffsetAndLimit(int $offset, int $limit): self
    {
        return new self($offset, $limit);
    }

}
