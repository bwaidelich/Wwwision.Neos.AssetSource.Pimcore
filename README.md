# Wwwision.Neos.AssetSource.Pimcore

[Pimcore](https://pimcore.com/) asset source for Neos CMS

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

## Pimcore

For the Pimcore API to work with this package, the following steps are required

### Active GraphQL configuration

A Datahub GraphQL configuration is required with *Read* access to the `Assset`, `Asset Folder` and `Asset Listing` types at least

### Image Thumbnail configurations

Two image thumbnail configurations should be created, in order to provide a good UX in the Neos backend:

* `thumbnail` with a *Contain* transformation of 250x250 px
* `preview` with a *Cover* transformation of 1200x1200 px
