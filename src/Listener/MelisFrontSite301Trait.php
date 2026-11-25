<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

/**
 * Finds the 301 redirect (MelisCms > Redirection 301) of the requested site (Mantis #0009351).
 *
 * The listeners compared the host with the FIRST domain of the current environment, whatever
 * its site, so only that site could redirect: on the others the redirect was found but never
 * sent (and the site 404 page was skipped too), giving a bare 404. The site is now taken from
 * the domain of the request, and a redirect of that site (or one without a site) is used.
 */
trait MelisFrontSite301Trait
{
    /**
     * @return string|null the new url, or null when the site has no redirect for this old url
     */
    protected function findSite301Url($sm, string $host, string $path): ?string
    {
        $redirects = $sm->get('MelisEngineTableSite301')->getEntryByField('s301_old_url', $path);
        if (empty($redirects) || !$redirects->count()) {
            return null;
        }

        // Site of the requested domain: the row of the current environment first, else any row.
        $siteId = null;
        $platform = (string) getenv('MELIS_PLATFORM');
        foreach ($sm->get('MelisEngineTableSiteDomain')->getEntryByField('sdom_domain', $host) as $domain) {
            if ($siteId === null || (string) $domain->sdom_env === $platform) {
                $siteId = (int) $domain->sdom_site_id;
            }
            if ((string) $domain->sdom_env === $platform) {
                break;
            }
        }

        $anySite = null;
        foreach ($redirects as $redirect) {
            if (empty($redirect->s301_site_id)) {
                $anySite = $anySite ?? (string) $redirect->s301_new_url;
            } elseif ($siteId !== null && (int) $redirect->s301_site_id === $siteId) {
                return (string) $redirect->s301_new_url;
            }
        }

        return $anySite;
    }
}
