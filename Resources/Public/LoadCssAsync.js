/*!
 * Carbon.IncludeAssets - created by Jon Uhlmann
 * @build 2017-10-16 20:30
 * @link https://github.com/jonnitto/Carbon.IncludeAssets
 */
!function(){"use strict";!function(e){e.loadCSS=function(t,n,r){function o(e){if(i.body)return e();setTimeout(function(){o(e)})}function a(){d.addEventListener&&d.removeEventListener("load",a),d.media=r||"all"}var l,i=e.document,d=i.createElement("link");if(n)l=n;else{var u=(i.body||i.getElementsByTagName("head")[0]).childNodes;l=u[u.length-1]}var s=i.styleSheets;d.rel="stylesheet",d.href=t,d.media="only x",o(function(){l.parentNode.insertBefore(d,n?l:l.nextSibling)});var c=function(e){for(var t=d.href,n=s.length;n--;)if(s[n].href===t)return e();setTimeout(function(){c(e)})};return d.addEventListener&&d.addEventListener("load",a),d.onloadcssdefined=c,c(a),d}}(window),function(e){if(e.loadCSS){var t=loadCSS.relpreload={};if(t.support=function(){try{return e.document.createElement("link").relList.supports("preload")}catch(e){return!1}},t.poly=function(){for(var t=e.document.getElementsByTagName("link"),n=0;n<t.length;n++){var r=t[n];"preload"===r.rel&&"style"===r.getAttribute("as")&&(e.loadCSS(r.href,r,r.getAttribute("media")),r.rel=null)}},!t.support()){t.poly();var n=e.setInterval(t.poly,300);e.addEventListener&&e.addEventListener("load",function(){t.poly(),e.clearInterval(n)}),e.attachEvent&&e.attachEvent("onload",function(){e.clearInterval(n)})}}}(window)}();

//# sourceMappingURL=LoadCssAsync.js.map
