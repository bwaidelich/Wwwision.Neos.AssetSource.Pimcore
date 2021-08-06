<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\ValueObject;

use Neos\Flow\Annotations as Flow;
use Webmozart\Assert\Assert;

/**
 * @Flow\Proxy(false)
 */
final class AssetOrderings implements \IteratorAggregate
{
    /**
     * @var AssetOrdering[]
     */
    private array $orderings;

    private function __construct(array $orderings)
    {
        $this->orderings = $orderings;
    }

    /**
     * @param AssetOrdering[] $orderings
     * @return self
     */
    public static function fromArray(array $orderings): self
    {
        Assert::allIsInstanceOf($orderings, AssetOrdering::class);
        return new self($orderings);
    }

    public static function default(): self
    {
        return new self([AssetOrdering::default()]);
    }

    /**
     * @return iterable<AssetOrdering>
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->orderings);
    }

    /**
     * @return array<string>
     */
    public function fieldNames(): array
    {
        return array_map(static fn(AssetOrdering $ordering) => $ordering->field->toString(), $this->orderings);
    }

    /**
     * @return array<string>
     */
    public function directions(): array
    {
        return array_map(static fn(AssetOrdering $ordering) => $ordering->descending ? 'DESC' : 'ASC', $this->orderings);
    }
}
