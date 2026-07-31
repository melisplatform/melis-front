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
     */
    private const BLOCKED_ASSET_KEYS = ['js', 'css', 'files'];

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
                $GetParameters = $request->getQuery();

                foreach ([$GetParameters, $request->getPost()] as $params) {
                    foreach (self::BLOCKED_ASSET_KEYS as $key) {
                        if ($params->offsetExists($key)) {
                            $params->offsetUnset($key);
                        }
                    }
                }

                foreach ($GetParameters as $key => $value)
                {
                    if (!is_array($value))
                        $request->getQuery()->set($key, htmlspecialchars(htmlentities($value), ENT_QUOTES, 'UTF-8'));
                    else
                    {

                        array_walk_recursive($value, function (&$val) {
                            $val = htmlentities($val);
                            $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        });

                        $request->getQuery()->set($key, $value);
                    }
                }
            },
            100);

        $this->listeners[] = $callBackHandler;
    }
}