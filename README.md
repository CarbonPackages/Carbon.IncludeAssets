[![Latest stable version]][packagist] [![Total downloads]][packagist] [![License]][packagist] [![GitHub forks]][fork] [![GitHub stars]][stargazers] [![GitHub watchers]][subscription]

# Carbon.IncludeAssets Package for Neos CMS

With this package, you get able to import all your CSS and Javascript assets with few lines of code in [`Settings.yaml`]. The best practice is to include `carbon/includeassets` into your `composer.json` from your site package. After that, you can just add your settings. Besides the filenames, you are also able to pass all your necessary attributes to the tags. If you are not able to provide a file extension, you can force the type via `(css)`, `(js)` or `(mjs)` at the end.

- You can define multiple files from multiple packages.
- You can pass the filenames as a string (comma separated) or as an array (recommended)
- If you want to add attributes, add it with square brackets e.g.  
  `Filename.js[async data-prop data-attr="true" class="-js-loader"]`
- If you want to get a file included inline, add the attribute `inline`: e.g. `Filename.css[inline]`
- You can add multiple resources per line. E. g. `Slider.js,Main.css,Footer.css[async class="footer-styles"],Header[inline class="header-styles"],//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js[async]`
- CSS can also be loaded asynchronously: Add `async` as an attribute, e.g. `Filename.css[async]`
- If you want to add Google Fonts, you can write them down the fonts, e.g. `Lato|Open+Sans:400,700` or `Lato|Open+Sans:400,700[async]`
- On internal files, a hash from the content of the file gets appended. Please be aware that you have to clear the cache from Neos to update the hash value. It is meant to have a cache buster on production projects.
- You can also give the browser some [resource hints]: Globally via the settings `Carbon.IncludeAssets.ResourceHints` or via adding a special type (`(preloadasset)`, `(preloadcss)`, `(preloadscript)` or `(modulepreload)`) at the end of a `file` entry.
- You can also include the content of HTML files (e.g. `Favicon.html`). Usefull for copy and paste tracking codes, favicons, etc. HTML files are always read from the inline path and ignore all attributes.

## Structure of the Settings

In [`Carbon.IncludeAssets`](Configuration/Settings.yaml#L19) following settings are available:

| Key                     |  Description                                                                                                               |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| `LoadJSforCSSAsynchron` | (boolean) If true the javascript for asynchronous CSS get inlined (If needed). Defaults to `true`                          |
| `GoogleFonts`           | (string) If set, these fonts will included from Google. E.g. `Lato\|Open+Sans:400,700` Defaults to `null`                  |
| `ResourceHints`         | (array) The setting, which global [resource hints] should be added.                                                        |
| `Default`               | (array) The default setting for a `Packages` entry. If a key is not set within a `Packages` entry, this value will be used |

### Structure of Packages entry

In `Carbon.IncludeAssets.Packages` you can define your packages, which should output assets. The keys get sorted first by numbers, then by characters. Like that, it is possible to define a load order for you packages. A single entry can have following entries (The Defaults are stored in [`Carbon.IncludeAssets.Default`](Configuration/Settings.yaml#L30)):

| Key                  |  Description                                                                                                                                                                                                                                            |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Package`            | (string) The package key. If it set to `SitePackage`, it will be replaced automatically with the package key from the site package. Defaults to `SitePackage`                                                                                           |
| `CacheBuster`        | (boolean) Append a hash value from the content of the file. Defaults to `true`                                                                                                                                                                          |
| `ConditionPrototype` | (string) If set, the files get only included if the fusion prototype returns a truthy value. Defaults to `null`                                                                                                                                         |
| `Wrapper`            | (string) If set, the generated tags will be wrapped. `{content}` will be replaced with the tags. Example: `'<!--[if lt IE 9]>{content}<![endif]-->'`                                                                                                    |
| `Path`               | (array) Define the files get loaded from. There are different paths for inline and linked files. Every type (`css`,`js`, `mjs`, `html`, `preloadasset`, `preloadcss`, `preloadscript` or `modulepreload`) can have a different path inside the Resources folder |
| `General`            | (array) Asset files who get loaded in live and backend view. Contains four entries: `Head`, `Body`, `HeadStart` and `BodyStart`                                                                                                                                                    |
| `Backend`            | (array) Asset files that get loaded only in the backend view. Contains four entries: `Head`, `Body`, `HeadStart` and `BodyStart`                                                                                                                                                   |
| `Live`               | (array) Asset files that get loaded only in the live view. Contains four entries: `Head`, `Body`, `HeadStart` and `BodyStart`                                                                                                                                                     |

## Example

Here is a small example:

```yaml
Carbon:
  IncludeAssets:
    LoadJSforCSSAsynchron: true
    GoogleFonts: "Lato|Open+Sans:400,700&display=swap[async]"
    ResourceHints:
      # Preconnect to these domains
      Preconnect:
        - "https://use.typekit.net"
        - "https://www.google-analytics.com"
        - "https://www.googletagmanager.com"

    Packages:
      # The keys get sorted first by numbers, then by characters.
      aa_Theme:
        # Because no Package is defined, SitePackage from the default
        # will be used and will set to the site package.

        # Asset files which get loaded in live and backend view
        General:
          # These assets get loaded in the <head> (at the start)
          HeadStart:
            - Favicons.html

          # These assets get loaded in the <head> (at the end)
          Head:
            # Preload this Javascript
            - JsForLaterUse.js(preloadscript)

            # Preload this asset
            - Logo.png[as="image"](preloadasset)

            # Load this CSS asynchronous
            - GeneralHead.css[async]

            # If a file has the attribute `inline`, the file gets
            # inlined. Also, a different path is used. This path is
            # set in under `Carbon.IncludeAssets.Default.Path.Inline.CSS`
            # and `Carbon.IncludeAssets.Default.Path.Inline.JS`
            - AboveTheFold.css[inline]

            # Run this javascript after to document is ready
            - GeneralHead.js[defer]

            # Add this javascript as a module
            - JavascriptModule.mjs

            # External files can also be defined.
            # It has to start with //, https:// or http://
            - //foo.bar/externalfile.js

            # If you can't provide a proper file extension you can force the type:
            - FileWithCustomFileExtension.ext(css)

            # This works also with external files
            - //foo.bar/externalfile.php[async](js)

          # This assets get loaded at the start of the <body>
          HeadStart:
            - NoscriptWarning.html

          # This assets get loaded at the end of the <body>
          Body:
            # You can also pass all attributes you want
            - GeneralBody.js[async class='-js-loader']

            # You can also pass further attributes if you use the inline attribute
            - AdditionSpecialFancyTracking.js[inline class="-js-tracking"]

        # Asset files which get loaded only in the backend
        Backend:
          # Arrays can also be defined like this
          Head: ["BackendHead.css", "BackendHead.js[inline]"]

        # Asset files which get loaded only in the live view
        Live:
          # You can set the value as a string, it will be automatically converted to an array
          Head: SingleLive.css
          Body: LiveBody.css,LiveBody.js[inline]

      # Example taken from Jonnitto.Plyr
      "zz_Jonnitto.Plyr":
        Package: "Jonnitto.Plyr"

        # The files get only included if the fusion prototype
        # Jonnitto.Plyr:IncludeCase` return a truthy value
        ConditionPrototype: "Jonnitto.Plyr:IncludeCase"

        # Ajust the paths for external files
        Path:
          File:
            CSS: "Public"
            JS: "Public"

        # Set the files
        General:
          Head:
            - plyr.min.js[defer]
            - plyr.css
```

Take a look at the [`Settings.yaml`]. There you see all the available options.

## Fusion Prototypes

Basically, you have to folders with Fusion Prototypes: [Internal](Resources/Private/Fusion/Internal) and [External](Resources/Private/Fusion/External). In the External folder you will find some prototypes which you can help you in your development:

### [Carbon.IncludeAssets:Case]

This prototype is a small helper to write prototypes for the `ConditionPrototype` setting. Return `true` or `false`.

| Property            | Description                                                                                            |
| ------------------- | ------------------------------------------------------------------------------------------------------ |
| `mixin`             | (string) The node type name of an mixin. Defaults to `null`                                            |
| `document`          | (string) The node type name the document type. Defaults to `this.mixin`                                |
| `content`           | (string) The node type name the content type. Defaults to `this.mixin`                                 |
| `contentCollection` | (string) The filter for the content collection. Defaults to `[instanceof Neos.Neos:ContentCollection]` |
| `documentNode`      | (node) The node from the document. Defaults to `documentNode`                                          |
| `alwaysInclude`     | (boolean) If `true`, the prototype return `true`. Defaults to `node.context.inBackend`                 |

### [Carbon.IncludeAssets:Collection]

This prototype generates your `script`, `style` and `link` tags from the files which are defined in the property `collection`.

| Property       | Description                                                                                                                                                               |
| -------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `collection`   | (array with strings) The collection with the filenames. (Example: ['Main.css','Main.js[defer]']) Defaults to `[]`                                                         |
| `assetPackage` | (string) The name of the package. (Example: `Jonnitto.Plyr`) Defaults to `null`                                                                                           |
| `cacheBuster`  | (boolean) Append a hash value from the content of the file. Defaults to the value set in the [`Settings.yaml`](Configuration/Settings.yaml#L32)                           |
| `paths`        | (array) The paths to the internal and external files inside the Resources folder. Defaults to the value set in the [`Settings.yaml`](Configuration/Settings.yaml#L34-L46) |
| `wrapper`      | (string) If set, the generated tags will be wrapped. `{content}` will be replaced with the tags. Example: `'<!--[if lt IE 9]>{content}<![endif]-->'`                      |

### [Carbon.IncludeAssets:File]

The heart of this package. This prototype generates a `script`, `style`, and `link` tag. You can pass a `file` (with or without the path). Be aware that you can also pass the attributes like described on top. To force a type you can write `(js)`, `(css)`, `(preloadasset)`, `(preloadcss)`, `(preloadscript)` or `(modulepreload)` at the end of `file`.

| Property       | Description                                                                                                                                     |
| -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- |
| `file`         | (string) The filename. You have to write it in the same way as it would be defined in the Settings.yaml file. Defaults to `null`                |
| `assetPackage` | (string) The name of the package. (Example: `Jonnitto.Plyr`) Defaults to `node.context.currentSite.siteResourcesPackageKey`                     |
| `cacheBuster`  | (boolean) Append a hash value from the content of the file. Defaults to the value set in the [`Settings.yaml`](Configuration/Settings.yaml#L32) |
| `assetPath`    | (string) The path to the file inside the Resources folder. Per default, it is set dynamically                                                   |
| `wrapper`      | (string) If set, the tag will be wrapped. `{content}` will be replaced with the tag. Example: `'<!--[if lt IE 9]>{content}<![endif]-->'`        |

### [Carbon.IncludeAssets:GoogleFonts]

You can set the property `fonts` e.g. `Lato|Open+Sans:400,700` and the `style` tag get generated. Per default, this prototype read the [`Settings.yaml`](Configuration/Settings.yaml#L21).

### [Carbon.IncludeAssets:ResourceHints]

This prototype renders the [resource hints] for the browser. Per default, this prototype read the [`Settings.yaml`](Configuration/Settings.yaml#L22-L28). But you can also pass `preloadNodes` or `prerenderNodes` (Array, FlowQuery, or a single node) for further optimization.

## Installation

Most of the time, you have to make small adjustments to a package (e.g., the configuration in [`Settings.yaml`]). Because of that, it is important to add the corresponding package to the composer from your theme package. Mostly this is the site package located under `Packages/Sites/`. To install it correctly go to your theme package (e.g.`Packages/Sites/Foo.Bar`) and run following command:

```bash
composer require carbon/includeassets --no-update
```

The `--no-update` command prevent the automatic update of the dependencies. After the package was added to your theme `composer.json`, go back to the root of the Neos installation and run `composer update`. Et voilà! Your desired package is now installed correctly.

[packagist]: https://packagist.org/packages/carbon/includeassets
[latest stable version]: https://poser.pugx.org/carbon/includeassets/v/stable
[total downloads]: https://poser.pugx.org/carbon/includeassets/downloads
[license]: https://poser.pugx.org/carbon/includeassets/license
[github forks]: https://img.shields.io/github/forks/CarbonPackages/Carbon.IncludeAssets.svg?style=social&label=Fork
[github stars]: https://img.shields.io/github/stars/CarbonPackages/Carbon.IncludeAssets.svg?style=social&label=Stars
[github watchers]: https://img.shields.io/github/watchers/CarbonPackages/Carbon.IncludeAssets.svg?style=social&label=Watch
[fork]: https://github.com/CarbonPackages/Carbon.IncludeAssets/fork
[stargazers]: https://github.com/CarbonPackages/Carbon.IncludeAssets/stargazers
[subscription]: https://github.com/CarbonPackages/Carbon.IncludeAssets/subscription
[`settings.yaml`]: Configuration/Settings.yaml
[carbon.includeassets:case]: Resources/Private/Fusion/External/Case.fusion
[carbon.includeassets:collection]: Resources/Private/Fusion/External/Collection.fusion
[carbon.includeassets:file]: Resources/Private/Fusion/External/File.fusion
[carbon.includeassets:googlefonts]: Resources/Private/Fusion/External/GoogleFonts.fusion
[carbon.includeassets:resourcehints]: Resources/Private/Fusion/External/ResourceHints.fusion
[resource hints]: https://www.smashingmagazine.com/2019/04/optimization-performance-resource-hints/
