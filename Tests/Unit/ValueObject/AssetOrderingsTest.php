<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\Tests\Unit\ValueObject;

use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetOrderings;
use PHPUnit\Framework\TestCase;

class AssetOrderingsTest extends TestCase
{

    /**
     * @test
     */
    public function default_orderings_are_applied(): void
    {
        self::assertSame(['modificationDate'], AssetOrderings::default()->fieldNames());
        self::assertSame(['DESC'], AssetOrderings::default()->directions());
    }

}
