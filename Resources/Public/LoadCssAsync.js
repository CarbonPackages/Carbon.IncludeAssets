/*!
 * Carbon.IncludeAssets - created by Jon Uhlmann
 * @build 2017-03-31 13:00
 * @link https://github.com/jonnitto/Carbon.IncludeAssets
 */
!function(){"use strict";!function(e,t){function n(e,n,r){function o(e){if(t.body)return e();setTimeout(function(){o(e)})}function l(e){for(var t=ss.href,n=c.length;n--;)if(c[n].href===t)return e;setTimeout(function(){l(e)})}function a(){d.addEventListener&&d.removeEventListener("load",a),d.media=r||"all"}var i,d=t.createElement("link"),c=t.styleSheets;if(n)i=n;else{var s=t.body||t.head.childNodes;i=s[s.length-1]}return d.rel="stylesheet",d.href=e,d.media="only x",o(function(){i.parentNode.insertBefore(d,n?i:i.nextSibling)}),d.addEventListener&&d.addEventListener("load",a),d.onloadcssdefined=l,l(a),d}e.loadCSS=n}(window,document),function(e){if(e.loadCSS){var t={};if(loadCSS.relpreload={},t.support=function(){try{return e.document.createElement("link").relList.supports("preload")}catch(e){return!1}},t.poly=function(){Array.prototype.slice.call(document.getElementsByTagName("link")).forEach(function(t){"preload"===t.rel&&"style"===t.getAttribute("as")&&(e.loadCSS(t.href,t),t.rel=null)})},!t.support()){t.poly();var n=e.setInterval(t.poly,300);e.addEventListener&&e.addEventListener("load",function(){e.clearInterval(n)}),e.attachEvent&&e.attachEvent("onload",function(){e.clearInterval(n)})}}}(window)}();
