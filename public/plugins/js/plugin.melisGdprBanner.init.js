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
