# Wwwision.Neos.AssetSource.Pimcore

Pimcore asset source for Neos CMS

## Setup

### Add repository to root `composer.json`:

```json
    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/bwaidelich/Wwwision.Neos.AssetSource.Pimcore"
      }
    ],
```

(this step is only required until the package is published on packagist.org)

### Install packge via composer

```
composer require wwwision/neos-assetsource-pimcore
```

### Configure Pimcore asset source

In a global (or site specific) `Settings.yaml`:

```yaml
Neos:
  Media:
    assetSources:
      'pimcore':
        assetSource: 'Wwwision\Neos\AssetSource\Pimcore\AssetSource\PimcoreAssetSource'
        assetSourceOptions:
          label: 'Pimcore'
          description: 'Pimcore Asset Source'

          api:
            baseUrl: '<https://pimcore-base.url>'
            endpoint: '<datahub configuration name>'
            apiKey: '<datahub api key>'
```

**Note:** Additional options for the HTTP client can be defined via `additionalConfiguration`. For example in order to disable SSL checks:

```yaml
Neos:
  Media:
    assetSources:
      'pimcore':
          # ...
          api:
            # ...
            additionalConfiguration:
              verify: false
```

### (Optionally) disable Neos asset source

If the Pimcore asset source should be used exlusivly, the built-in "neos" asset source can be disabled globally via `Settings.yaml`:

```yaml
Neos:
  Media:
    assetSources:
      'neos': ~
```
