window.MelisGdprBanner = (function () {
    /**
     * To make a "persistent cookie" (a cookie that "never expires"),
     * we need to set a date/time in a distant future (one that possibly exceeds the user's
     * machine life).
     *
     * src: https://stackoverflow.com/a/22479460/7870472
     */
    const MAX_COOKIE_AGE = 2147483647000;
    const BANNER_COOKIE_NAME = "melis-gdpr-banner-cookie";
    const BANNER_CONTAINER = "melis-gdpr-banner-container";

    /**
     * Usage : setCookie('user', 'John', {secure: true, 'expires': 3600});
     * @param name
     * @param value
     * @param options
     */
    function setCookie(name, value, options = {}) {
        let defaultOptions = {
            path: '/',
            expires: new Date(MAX_COOKIE_AGE).toUTCString()
        };
        /**
         * Construct the cookie:
         *  - Merge the "defaultOptions" & the passed parameter "options"
         *  (overwriting "defaultOptions").
         * - Convert merged object into array (ease of iteration) & loop
         *
         * src: https://zellwk.com/blog/looping-through-js-objects/
         */
        options = Object.entries({...defaultOptions, ...options});
        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
        for (const [key, value] of options) {
            updatedCookie += "; " + key + "=" + value;
        }

        document.cookie = updatedCookie;
    }

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace("/([\.$?*|{}\(\)\[\]\\\/\+^])/g", '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function deleteCookie(name) {
        setCookie(name, "", {
            'expires': 0
        })
    }

    return {
        setCookie: setCookie,
        getCookie: getCookie,
        deleteCookie: deleteCookie,
        /** You can always set a different cookie name & banner container*/
        cookieName: BANNER_COOKIE_NAME,
        container: BANNER_CONTAINER,
    };
})();

/**
 * When customizing your own view file, please place set the ff:
 * - "data-render-mode"
 * - "data-plugin-id
 * - your container id & class
 */
function melisGdprBanner_init(pluginId) {
    if (pluginId === undefined) {
        /** pluginId is initially "undefined" when the plugin is loaded in front */
        let bannerContainer = $("." + MelisGdprBanner.container);
        if (bannerContainer.length > 0) {
            bannerContainer.each(function () {
                pluginId = this.id;
            });
        }
    }

    if (typeof pluginId !== 'undefined' && pluginId) {
        let banner = $("#" + pluginId);
        let renderMode = banner.data("renderMode");

        let bannerCookie = MelisGdprBanner.getCookie(MelisGdprBanner.cookieName);
        if (renderMode === "front" && bannerCookie === undefined) {
            /** User's first visit: Declare banner cookie for this site */
            MelisGdprBanner.setCookie(MelisGdprBanner.cookieName, "true");
            bannerCookie = "true";
        }

        if (renderMode === "melis" || bannerCookie === "true") {
            /** Show banner if bannerCookie is true || during back office page edition */
            banner.show();
        }
    }
}

$(document).ready(function () {
    let $body = $("body");

    /** Agree to the site's cookie policy */
    $body.on('click', '.melis-gdpr-banner-agree', function () {
        let banner = $body.find(this).data();

        if (typeof banner.renderMode !== 'undefined' && banner.renderMode === 'front') {
            MelisGdprBanner.setCookie(MelisGdprBanner.cookieName, false);
        }

        if (typeof banner.pluginId !== 'undefined' && banner.pluginId) {
            banner = $body.find('#' + banner.pluginId);
            if (banner.length > 0) {
                banner.slideToggle('fast');
            }
        }
    });
});

melisGdprBanner_init();
