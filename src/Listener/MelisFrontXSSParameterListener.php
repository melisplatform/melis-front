<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use MelisCore\Listener\MelisGeneralListener;

class MelisFrontXSSParameterListener extends MelisGeneralListener implements ListenerAggregateInterface
{
    /**
     * Request keys that can be coerced into front-end asset URLs by the
     * MelisTemplatingPlugin config-merge. None of these are legitimate
     * user-supplied parameters, so they are always removed.
     *
     * SECURITY (audit FE-H4 / SL-H1): MelisTemplatingPlugin::formatGetPostInArray()
     * rebuilds nested config from keys split on "-" (e.g. files-js-0 → files[js][0]),
     * so blocking only the exact top-level names left the EXPLODED form through — any
     * plugin declaring `files` in its front config then emitted an attacker-controlled
     * <script src>. We therefore strip both the exact names and anything prefixed
     * `files-`/`js-`/`css-`.
     */
    private const BLOCKED_ASSET_KEYS = ['js', 'css', 'files'];

    private function isBlockedAssetKey(string $key): bool
    {
        if (in_array($key, self::BLOCKED_ASSET_KEYS, true)) {
            return true;
        }
        foreach (self::BLOCKED_ASSET_KEYS as $prefix) {
            if (strpos($key, $prefix . '-') === 0) {
                return true;
            }
        }
        return false;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_ROUTE,
            function(MvcEvent $e){

                // Sanitize query/POST parameters for ALL front requests.
                // A previous extension-based early-return (skipping any URI whose
                // path contained a non-".php" segment, e.g. "/foo.html?q=...")
                // trivially defeated this sanitizer, so it has been removed.

                $request = $e->getRequest();

                // SECURITY (audit SL-H1/SL-H2): sanitize GET *and* POST symmetrically.
                // The previous version only strip-listed POST asset keys but never
                // html-encoded POST values, so any plugin config key reflected from a
                // POST body (e.g. the plugin `id` echoed into an attribute) was a
                // reflected XSS reachable by an auto-submitted cross-site form.
                foreach ([$request->getQuery(), $request->getPost()] as $params) {
                    foreach ($params as $key => $value) {
                        if ($this->isBlockedAssetKey((string) $key)) {
                            $params->offsetUnset($key);
                            continue;
                        }
                        if (!is_array($value)) {
                            $params->set($key, htmlspecialchars(htmlentities($value), ENT_QUOTES, 'UTF-8'));
                        } else {
                            array_walk_recursive($value, function (&$val) {
                                $val = htmlentities($val);
                                $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                            });
                            $params->set($key, $value);
                        }
                    }
                }
            },
            100);

        $this->listeners[] = $callBackHandler;
    }
}