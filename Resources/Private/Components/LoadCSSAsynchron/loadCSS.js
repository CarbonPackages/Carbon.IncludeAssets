// loadCSS: load a CSS file asynchronously. [c]2016 @scottjehl, Filament Group, Inc. Licensed MIT */
'use strict';
((root, doc) => {
	// exported loadCSS
	function loadCSS(href, before, media) {
		// Arguments explained:
		// `href` [REQUIRED] is the URL for your CSS file.
		// `before` [OPTIONAL] is the element the script should use as a reference for injecting our stylesheet <link> before
			// By default, loadCSS attempts to inject the link after the last stylesheet or script in the DOM. However, you might desire a more specific location in your document.
		// `media` [OPTIONAL] is the media type or query of the stylesheet. By default it will be 'all'
		let styleSheet = doc.createElement('link');
		let sheets = doc.styleSheets;
		let ref;

		if (before) {
			ref = before;
		} else {
			let refs = (doc.body || doc.head.childNodes);
			ref = refs[refs.length - 1];
		}

		styleSheet.rel = 'stylesheet';
		styleSheet.href = href;
		// temporarily set media to something inapplicable to ensure it'll fetch without blocking render
		styleSheet.media = 'only x';

		// wait until body is defined before injecting link. This ensures a non-blocking load in IE11.
		function ready(callback) {
			if (doc.body) {
				return callback();
			}
			setTimeout(() => {
				ready(callback);
			});
		}

		// Inject link
			// Note: the ternary preserves the existing behavior of "before" argument, but we could choose to change the argument to "after" in a later release and standardize on ref.nextSibling for all refs
			// Note: `insertBefore` is used instead of `appendChild`, for safety re: http://wwroot.paulirish.com/2011/surefire-dom-element-insertion/
		ready(() => {
			ref.parentNode.insertBefore(styleSheet, (before ? ref : ref.nextSibling));
		});

		// A method (exposed on return object for external use) that mimics onload by polling document.styleSheets until it includes the new sheet.
		function onloadcssdefined(callback) {
			let resolvedHref = ss.href;
			let i = sheets.length;
			while(i--) {
				if (sheets[i].href === resolvedHref) {
					return callback
				}
			}

			setTimeout(() => {
				onloadcssdefined(callback);
			});
		}

		function loadCB() {
			if (styleSheet.addEventListener) {
				styleSheet.removeEventListener('load', loadCB);
			}
			styleSheet.media = media || 'all';
		}

		// once loaded, set link's media back to `all` so that the stylesheet applies once it loads
		if (styleSheet.addEventListener) {
			styleSheet.addEventListener('load', loadCB);
		}
		styleSheet.onloadcssdefined = onloadcssdefined;
		onloadcssdefined(loadCB);
		return styleSheet;
	}

	root.loadCSS = loadCSS;

})(window, document);
