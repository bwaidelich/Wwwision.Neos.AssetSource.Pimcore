<?php
declare(strict_types=1);
namespace Wwwision\Neos\AssetSource\Pimcore\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetFilter;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\AssetOrderings;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\Pagination;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\PimcoreAsset;
use Wwwision\Neos\AssetSource\Pimcore\ValueObject\PimcoreAssetList;
use Neos\Utility\Arrays;
use Psr\Http\Message\UriInterface;

final class PimcoreClient
{
    private Client $httpClient;
    private UriInterface $baseUrl;
    private string $endpoint;

    public function __construct(UriInterface $baseUrl, string $endpoint, string $apiKey, array $additionalConfiguration)
    {
        $this->baseUrl = $baseUrl;
        $this->endpoint = $endpoint;
        $config = Arrays::arrayMergeRecursiveOverrule($additionalConfiguration, ['base_uri' => $this->baseUrl, 'headers' => ['X-API-Key' => $apiKey]]);
        $this->httpClient = new Client($config);
    }

    public function getAssets(AssetFilter $filter, AssetOrderings $orderings, Pagination $pagination): PimcoreAssetList
    {
        $query = 'query getAssets($filter: String, $sortBy: [String], $sortOrder: [String], $limit: Int!, $offset: Int) {
          getAssetListing(filter: $filter, sortBy: $sortBy, sortOrder: $sortOrder, first: $limit, after: $offset) {
            totalCount
            edges {
              cursor
              node {
                ... AssetFields
              }
            }
          }
        }
        ' . $this->assetFragment();

        $data = $this->query($query, [
            'limit' => $pagination->limit,
            'offset' => $pagination->offset,
            'filter' => $filter->toFilterString(),
            'sortBy' => $orderings->fieldNames(),
            'sortOrder' => $orderings->directions(),
        ]);
        return PimcoreAssetList::fromApiResult($data['getAssetListing'], $this->baseUrl);
    }

    public function getAsset(int $id): PimcoreAsset
    {
        $query = 'query getAsset($id:Int!) {
          getAsset(id:$id) {
            ... AssetFields
          }
        }
        ' . $this->assetFragment();
        $data = $this->query($query, ['id' => $id]);
        return PimcoreAsset::fromApiResult($data['getAsset'], $this->baseUrl);
    }

    private function query(string $query, array $variables): array
    {
        $url = '/pimcore-graphql-webservices/' . $this->endpoint;
        try {
            $response = $this->httpClient->post($url, [
                'json' => compact('query', 'variables')
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException(sprintf('Failed to POST to "%s": %s', $url, $e->getMessage()), 1628265822, $e);
        }
        $contents = $response->getBody()->getContents();
        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException(sprintf('Failed to JSON decode API response: %s', $e->getMessage()), 1628265421, $e);
        }
        if (isset($data['errors'])) {
            throw new \RuntimeException(sprintf('Error from Pimcore API: %s', $contents));
        }
        return $data['data'];
    }

    private function assetFragment(): string
    {
        return 'fragment AssetFields on asset {
          id
          filename
          filesize
          fullpath
          fullpath_thumbnail: fullpath(thumbnail: "thumbnail")
          fullpath_preview: fullpath(thumbnail: "preview")
          mimetype
          metadata {
            name
            data
          }
          creationDate
          modificationDate
        }';
    }
}
