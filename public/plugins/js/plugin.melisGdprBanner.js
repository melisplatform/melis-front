window.MelisFrontGdprBanner = (function () {
    /**
     * To make a "persistent cookie" (a cookie that "never expires"),
     * we need to set a date/time in a distant future (one that possibly exceeds the user's
     * machine life).
     *
     * src: https://stackoverflow.com/a/22479460/7870472
     */
    const MAX_COOKIE_AGE = 2147483647000;
    const BANNER_COOKIE_NAME = "melis-cms-gdpr-banner-cookie";
    const BANNER_CONTAINER = "melis-cms-gdpr-banner-container";

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
        /** You can always use a different cookie name */
        cookieName: BANNER_COOKIE_NAME,
        container: BANNER_CONTAINER,
    };
})();
