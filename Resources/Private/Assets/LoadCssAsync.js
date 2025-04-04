/*! loadCSS. [c]2017 Filament Group, Inc. MIT License */
/* This file is meant as a standalone workflow for
- testing support for link[rel=preload]
- enabling async CSS loading in browsers that do not support rel=preload
- applying rel preload css once loaded, whether supported or not.
*/

const win = window;
const doc = win.document;

function addEventListener(element, callback) {
    if (element.addEventListener) {
        element.addEventListener("load", callback);
    } else if (element.attachEvent) {
        element.attachEvent("onload", callback);
    }
}

// rel=preload support test
if (!win.loadCSS) {
    win.loadCSS = function () {};
}

// define on the loadCSS obj
let rp = (loadCSS.relpreload = {});

// rel=preload feature support test
// runs once and returns a function for compat purposes
rp.support = (function () {
    let ret;
    try {
        ret = doc.createElement("link").relList.supports("preload");
    } catch (e) {
        ret = false;
    }
    return function () {
        return ret;
    };
})();

// if preload isn't supported, get an asynchronous load by using a non-matching media attribute
// then change that media back to its intended value on load
rp.bindMediaToggle = function (link) {
    // remember existing media attr for ultimate state, or default to 'all'
    let finalMedia = link.media || "all";

    function enableStylesheet() {
        link.media = finalMedia;
    }

    // bind load handlers to enable media
    addEventListener(link, enableStylesheet);

    // Set rel and non-applicable media type to start an async request
    // note: timeout allows this to happen async to let rendering continue in IE
    setTimeout(function () {
        link.rel = "stylesheet";
        link.media = "only x";
    }, 0);
    // also enable media after 3 seconds,
    // which will catch very old browsers (android 2.x, old firefox) that don't support onload on link
    setTimeout(enableStylesheet, 3000);
};

// loop through link elements in DOM
rp.poly = function () {
    // double check this to prevent external calls from running
    if (rp.support()) {
        return;
    }
    const links = doc.getElementsByTagName("link");
    for (let i = 0; i < links.length; i++) {
        let link = links[i];
        // qualify links to those with rel=preload and as=style attrs
        if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) {
            // prevent rerunning on link
            link.setAttribute("data-loadcss", true);
            // bind listeners to toggle media back
            rp.bindMediaToggle(link);
        }
    }
};

// if unsupported, run the polyfill
if (!rp.support()) {
    // run once at least
    rp.poly();

    // rerun poly on an interval until onload
    let run = win.setInterval(rp.poly, 500);
    addEventListener(win, function () {
        rp.poly();
        win.clearInterval(run);
    });
}
