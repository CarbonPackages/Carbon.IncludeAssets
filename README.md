Carbon.IncludeAssets Package for Neos CMS
=========================================

With this package, you get able to import all your CSS and Javascript assets with few lines of code in [Settings.yaml](Configuration/Settings.yaml). The best practice is to include `carbon/includeassets` into your `composer.json` from your site package. After that, you just can add your settings. Besides the filenames, you are also able to pass all your necessary attributes to the tags. If you are note able to provide a fileextension you can force the type via `(js)` or `(css)` at the end.

* You can pass the filenames as string (comma separated) or as an array
* If you want to add attributes, add it with square brackets e.g. `Filename.js[async data-prop data-attr="true" class="-js-loader"]`
* If you want to get a file included inline, just add the attribute `inline`: e.g. `Filename.css[inline]`
* You can add multiple resources per line. E. g. `Slider.js,Main.ss,//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js[async]`
* If you want to add Google Fonts, you just can write the down the fonts, e.g. `Lato|Open+Sans:400,700`
* On internal files, a md5 hash from the content of the file gets appended. Please be aware that you have to clear the cache from Neos to update the hash value. It is meant to have a cache buster on production projects.

Here is a small example with strings:

```
Carbon:
  IncludeAssets:
    GoogleFonts: 'Lato|Open+Sans:400,700'
    # Asset files who get's loaded in live and backend view
    General:
      Head: GeneralHead.css,AboveTheFold.css[inline],GeneralHead.js[async defer]
      Body: GeneralBody.js[async defer class='-js-loader'],AdditionSpecialFancyTracking.js[inline]
    # Asset files who get's loaded only in the backend
    Backend:
      Head: BackendHead.css,BackendHead.js
    # Asset files who get's loaded only in the live view
    Live:
      Body: LiveBody.css,LiveBody.js
```

Here is a small example with arrays: You can write them in one line, or in multiple lines

```
Carbon:
  IncludeAssets:
    GoogleFonts: 'Lato|Open+Sans:400,700'
    # Asset files who get's loaded in live and backend view
    General:
      Head: ['GeneralHead.css','AboveTheFold.css[inline]','GeneralHead.js[async defer]']
      Body:
        - GeneralBody.js[async defer class='-js-loader']
        - AdditionSpecialFancyTracking.js[inline]
    # Asset files who get's loaded only in the backend
    Backend:
      Head:
        - BackendHead.css
        - BackendHead.js
    # Asset files who get's loaded only in the live view
    Live:
      Body:
        - LiveBody.css
        - LiveBody.js
```

Installation
------------

Most of the time you have to make small adjustments to a package (e.g. configuration in `Settings.yaml`). Because of that, it is important to add the corresponding package to the composer from your theme package. Mostly this is the site packages located under `Packages/Sites/`. To install it correctly go to your theme package (e.g.`Packages/Sites/Foo.Bar`) and run following command:
```
composer require carbon/includeassets --no-update
```

The `--no-update` command prevent the automatic update of the dependencies. After the package was added to your theme `composer.json`, go back to the root of the Neos installation and run `composer update`. Et voilà! Your desired package is now installed correctly.


License
-------

Licensed under MIT, see [LICENSE](LICENSE)
