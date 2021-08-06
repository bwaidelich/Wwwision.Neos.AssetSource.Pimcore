<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\Tests\Unit\ValueObject;

use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetFilter;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetType;
use PHPUnit\Framework\TestCase;

class AssetFilterTest extends TestCase
{

    public function filter_tests_dataProvider(): array
    {
        $filter = AssetFilter::default();
        return [
            [$filter, ['type' => ['$not' => 'folder']]],
            [$filter->forAssetType(AssetType::ALL()), ['type' => ['$not' => 'folder']]],
            [$filter->forSearchTerm('foo'), ['$and' => [['type' => ['$not' => 'folder']], ['filename' => ['$like' => '%foo%']]]]],
            [$filter->forAssetType(AssetType::AUDIO()), ['type' => 'audio']],
            [$filter->forAssetType(AssetType::DOCUMENT()), ['type' => 'document']],
            [$filter->forAssetType(AssetType::IMAGE()), ['type' => 'image']],
            [$filter->forAssetType(AssetType::VIDEO()), ['type' => 'video']],
            [$filter->forSearchTerm('search term')->forAssetType(AssetType::IMAGE()), ['$and' => [['type' => 'image'], ['filename' => ['$like' => '%search term%']]]]],
            [$filter->forSearchTerm('search term')->forAssetType(AssetType::ALL()), ['$and' => [['type' => ['$not' => 'folder']], ['filename' => ['$like' => '%search term%']]]]],
        ];
    }

    /**
     * @test
     * @dataProvider filter_tests_dataProvider
     */
    public function filter_tests(AssetFilter $filter, array $expectedResult): void
    {
        self::assertSame($expectedResult, $filter->toFilterArray());
    }

}
