Carbon.IncludeAssets Package for Neos CMS
=========================================

With this package, you get able to import all your CSS and Javascript assets with few lines of code in [Settings.yaml](Configuration/Settings.yaml). The best practice is to include `carbon/includeassets` into your `composer.json` from your site package. After that, you just can add your settings. If you enter the filenames, please add them **without** the file extension, Fusion will do that for you. Besides the filenames you are also able to pass all your needed attributes to the tags.

* If you want to add attributes, add it with square brackets e.g. `Filename[async data-prop data-attr="true"]`
* If you want a file included inline, just add the attribute "inline": e.g. `Filename[inline]`
* You can add multiple resources per line. E. g. `Slider,Main,//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js[async]`
* If you want to add Google Fonts, you just can write the down the fonts, e.g. `Lato|Open+Sans:400,700`
* On internal files, a md5 hash from the content of the file gets appended. Please be aware that you have to clear the cache from Neos to update the hash value. It is ment to have an cache buster on production projects.

Here is a small example:

```
Carbon:
  IncludeAssets:
    GoogleFonts: 'Lato|Open+Sans:400,700'
    LoadCSSAsynchron: true
    LoadJSforCSSAsynchron: true
    # Asset files who get's loaded in live and backend view
    General:
      Head:
        Style: GeneralHead,AboveTheFold[inline]
        Script: GeneralHead[async defer]
      Body:
        Script: GeneralBody[async defer],AdditionSpecialFancyTracking[inline]
    # Asset files who get's loaded only in the backend
    Backend:
      Head:
        Style: BackendHead
        Script: BackendHead
    # Asset files who get's loaded only in the live view
    Live:
      Body:
        Style: LiveBody
        Script: LiveBody
```


Installation
------------

```
composer require carbon/includeassets
```


License
-------

Licensed under MIT, see [LICENSE](LICENSE)
