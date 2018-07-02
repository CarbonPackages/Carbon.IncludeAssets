[![Latest Stable Version](https://poser.pugx.org/carbon/includeassets/v/stable)](https://packagist.org/packages/carbon/includeassets)
[![Total Downloads](https://poser.pugx.org/carbon/includeassets/downloads)](https://packagist.org/packages/carbon/includeassets)
[![License](https://poser.pugx.org/carbon/includeassets/license)](LICENSE)
[![GitHub forks](https://img.shields.io/github/forks/CarbonPackages/Carbon.IncludeAssets.svg?style=social&label=Fork)](https://github.com/CarbonPackages/Carbon.IncludeAssets/fork)
[![GitHub stars](https://img.shields.io/github/stars/CarbonPackages/Carbon.IncludeAssets.svg?style=social&label=Stars)](https://github.com/CarbonPackages/Carbon.IncludeAssets/stargazers)
[![GitHub watchers](https://img.shields.io/github/watchers/CarbonPackages/Carbon.IncludeAssets.svg?style=social&label=Watch)](https://github.com/CarbonPackages/Carbon.IncludeAssets/subscription)

# Carbon.IncludeAssets Package for Neos CMS

With this package, you get able to import all your CSS and Javascript assets with few lines of code in [Settings.yaml](Configuration/Settings.yaml). The best practice is to include `carbon/includeassets` into your `composer.json` from your site package. After that, you just can add your settings. Besides the filenames, you are also able to pass all your necessary attributes to the tags. If you are not able to provide a file extension, you can force the type via `(css)`, `(js)` or `(mjs)` at the end.

*   You can define multiple files from multiple packages.
*   You can pass the filenames as a string (comma separated) or as an array (recommended)
*   If you want to add attributes, add it with square brackets e.g.  
    `Filename.js[async data-prop data-attr="true" class="-js-loader"]`
*   If you want to get a file included inline, add the attribute `inline`: e.g. `Filename.css[inline]`
*   You can add multiple resources per line. E. g. `Slider.js,Main.css,Footer.css[async class="footer-styles"],Header[inline class="header-styles"],//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js[async]`
*   CSS can also be loaded asynchronous: Add `async` as attribute, e.g. `Filename.css[async]`
*   If you want to add Google Fonts, you can write them down the fonts, e.g. `Lato|Open+Sans:400,700` or `Lato|Open+Sans:400,700[async]`
*   On internal files, a hash from the content of the file gets appended. Please be aware that you have to clear the cache from Neos to update the hash value. It is meant to have a cache buster on production projects.

Here is a small example:

```yaml
Carbon:
  IncludeAssets:
    LoadJSforCSSAsynchron: true
    GoogleFonts: 'Lato|Open+Sans:400,700[async]'
    Packages:
      # The keys get sorted first by numbers, then by characters.
      aa_Theme:
        # SitePackage is the default value. It will be replaced
        # automaticly with the package key from the site theme
        Package: SitePackage
        # Asset files who get loaded in live and backend view
        General:
          Head:
            - GeneralHead.css[async]
            # If a file has the attribute `inline`, the file get
            # inlined. Also a different path is used. This path is
            # set in under `Carbon.IncludeAssets.Default.Path.inline`
            - AboveTheFold.css[inline]
            - GeneralHead.js[defer]
            - JavascriptModule.mjs
            # External files can also be defined.
            # It has to start with //, https:// or http://
            - //foo.bar/externalfile.js
            # If you can't provide a proper file extension you can force the type:
            - FileWithCustomFileExtension.ext(css)
            # This works also with external files
            - //foo.bar/externalfile.php[async](js)
          Body:
            - GeneralBody.js[async class='-js-loader']
            # You can also pass further attributes if you use the inline attribute
            - AdditionSpecialFancyTracking.js[inline class="-js-tracking"]
        # Asset files who get loaded only in the backend
        Backend:
          # Arrays can be also definded like this
          Head: ['BackendHead.css', 'BackendHead.js']
        # Asset files who get loaded only in the live view
        Live:
          # You can set the value as a string, it will be automically converted to an array
          Head: SingleLive
          Body: LiveBody.css,LiveBody.js
      # Example taken from Jonnitto.Plyr
      'zz_Jonnitto.Plyr':
        Package: 'Jonnitto.Plyr'
        # The files get only included if the fusion prototype
        #  Jonnitto.Plyr:IncludeCase` return a truthy value
        ConditionPrototype: 'Jonnitto.Plyr:IncludeCase'
        # Ajust the paths for external files
        Path:
          File:
            CSS: 'Public'
            JS: 'Public'
        General:
          Head:
            - plyr.min.js[defer]
            - plyr.css
```

Take a look at the [Settings.yaml](Configuration/Settings.yaml). There you see all available options.

## Fusion Prototypes

Basically, you have to folders with Fusion Prototypes: [Internal](Resources/Private/Fusion/Internal) and [External](Resources/Private/Fusion/External). In the External folder you will find some prototypes who you can help you in your development:

### [Carbon.IncludeAssets:Collection](Resources/Private/Fusion/External/Collection.fusion)

This prototype generates your `script`, `style` and `link` tags from the files who are defined in the property `collection`.

| Property       | Description                                                                                                                                   |
| -------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| `collection`   | (array with strings) The collection with the filenames. (Example: ['Main.css','Main.js[defer]']) Defaults to `[]`                             |
| `assetPackage` | (string) The name of the package. (Example: `Jonnitto.Plyr`) Defaults to `null`                                                               |
| `cacheBuster`  | (boolean) Append a hash value from the content of the file. Defaults to the value set in the [Settings.yaml](Configuration/Settings.yaml#L25) |
| `paths`        | (array) The paths to the internal and external files. Defaults to the value set in the [Settings.yaml](Configuration/Settings.yaml#L27)       |

### [Carbon.IncludeAssets:File](Resources/Private/Fusion/External/File.fusion)

The heart of this package. This prototype generates a `script`, `style` and `link` tag. You can pass a `file` (with or without the path). Be aware that you can also pass the attributes like described on top. To force a type you can write `(js)` or `(css)` at the end of `file`.

| Property       | Description                                                                                                                                   |
| -------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| `file`         | (string) The filename. You have to write it in the same way as it would be defined in the Settings.yaml file. Defaults to `null`              |
| `assetPackage` | (string) The name of the package. (Example: `Jonnitto.Plyr`) Defaults to `node.context.currentSite.siteResourcesPackageKey`                   |
| `cacheBuster`  | (boolean) Append a hash value from the content of the file. Defaults to the value set in the [Settings.yaml](Configuration/Settings.yaml#L25) |
| `assetPath`    | (string) The path to the file. Per default it is set dynamically                                                                              |

### [Carbon.IncludeAssets:GoogleFonts](Resources/Private/Fusion/External/GoogleFonts.fusion)

You can set the property `fonts` e.g. `Lato|Open+Sans:400,700` and the `style` tag get generated. Per default, this prototype read the [Settings.yaml](Configuration/Settings.yaml#L21).

## Installation

Most of the time you have to make small adjustments to a package (e.g., the configuration in `Settings.yaml`). Because of that, it is important to add the corresponding package to the composer from your theme package. Mostly this is the site package located under `Packages/Sites/`. To install it correctly go to your theme package (e.g.`Packages/Sites/Foo.Bar`) and run following command:

```bash
composer require carbon/includeassets --no-update
```

The `--no-update` command prevent the automatic update of the dependencies. After the package was added to your theme `composer.json`, go back to the root of the Neos installation and run `composer update`. Et voilà! Your desired package is now installed correctly.

## License

Licensed under MIT, see [LICENSE](LICENSE)
