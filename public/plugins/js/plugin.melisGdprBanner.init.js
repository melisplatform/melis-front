var MelisGdprBanner = (function () {
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
		var defaultOptions = {
			path: "/",
			expires: new Date(MAX_COOKIE_AGE).toUTCString(),
		};
		/**
		 * Construct the cookie:
		 *  - Merge the "defaultOptions" & the passed parameter "options"
		 *  (overwriting "defaultOptions").
		 * - Convert merged object into array (ease of iteration) & loop
		 *
		 * src: https://zellwk.com/blog/looping-through-js-objects/
		 */
		options = Object.entries({ defaultOptions, options });

		var updatedCookie =
			encodeURIComponent(name) +
			"=" +
			encodeURIComponent(value) +
			"; SameSite=Lax";
		for (const [key, value] of options) {
			if (typeof value === "object") {
				$.each(value, (k, v) => {
					updatedCookie += "; " + k + "=" + v;
				});
			}
		}

		document.cookie = updatedCookie;
	}

	function getCookie(name) {
		var matches = document.cookie.match(
			new RegExp(
				"(?:^|; )" +
					name.replace("/([.$?*|{}()[]\\/+^])/g", "\\$1") +
					"=([^;]*)"
			)
		);
		return matches ? decodeURIComponent(matches[1]) : undefined;
	}

	function deleteCookie(name) {
		setCookie(name, "", {
			expires: 0,
		});
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

function melisGdprBanner_init(pluginId) {
	if (pluginId === undefined) {
		/** pluginId is initially "undefined" when the plugin is loaded in front */
		var bannerContainer = $("." + MelisGdprBanner.container);
		if (bannerContainer.length > 0) {
			bannerContainer.each(function () {
				pluginId = this.id;
			});
		}
	}

	if (typeof pluginId !== "undefined" && pluginId) {
		var banner = $("#" + pluginId);
		var isBackOffice = banner.data("isBo");

		// no showing this dialog in BO
		if (isBackOffice) return;

		var bannerCookie = MelisGdprBanner.getCookie(MelisGdprBanner.cookieName);
		if (bannerCookie === undefined || bannerCookie === "true") {
			/** User's first visit: Declare banner cookie for this site */
			MelisGdprBanner.setCookie(MelisGdprBanner.cookieName, "true");

			banner.show();
		}
	}
}

$(document).ready(function () {
	var $body = $("body");

	/** Agree to the site's cookie policy */
	$body.on("click", ".melis-gdpr-banner-agree", function () {
		var banner = $body.find(this).data();

		if (typeof banner.isBo !== "undefined" && banner.isBo === false) {
			MelisGdprBanner.setCookie(MelisGdprBanner.cookieName, false);
		}

		if (typeof banner.pluginId !== "undefined" && banner.pluginId) {
			banner = $body.find("#" + banner.pluginId);
			if (banner.length > 0) {
				banner.slideToggle("fast");
			}
		}
	});
});

melisGdprBanner_init();
