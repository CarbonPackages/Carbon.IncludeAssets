'use strict';
// CSS rel=preload polyfill. Depends on loadCSS function.
(root => {
	// rel=preload support test
	if(!root.loadCSS) {
		return;
	}
	let rp = {};
	loadCSS.relpreload = {};
	rp.support = () => {
		try {
			return root.document.createElement('link').relList.supports('preload');
		} catch (e) {
			return false;
		}
	};

	// loop preload links and fetch using loadCSS
	rp.poly = () => {
		let links = Array.prototype.slice.call(document.getElementsByTagName('link'));
		links.forEach(link => {
			if(link.rel === 'preload' && link.getAttribute('as') === 'style') {
				root.loadCSS(link.href, link);
				link.rel = null;
			}
		});
	};

	// if link[rel=preload] is not supported, we must fetch the CSS manually using loadCSS
	if(!rp.support()) {
		rp.poly();
		let run = root.setInterval(rp.poly, 300);

		if(root.addEventListener){
			root.addEventListener('load', () => {
				root.clearInterval(run);
			});
		}
		if(root.attachEvent){
			root.attachEvent('onload', () => {
				root.clearInterval(run);
			});
		}
	}
})(window);
