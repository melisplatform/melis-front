<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Segment;

/**
 * Page Cache Listener
 */
class MelisFrontPageCacheListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_FINISH,
            function(MvcEvent $e){
                
                // Get route match to know if we are displaying in back or front
                $routeMatch = $e->getRouteMatch();

                if($routeMatch) {

                    $params = $routeMatch->getParams();

                    if (!empty($params['idpage']) && $params['renderMode'] == 'front')
                    {
                        $params = $routeMatch->getParams();
                        $response = $e->getResponse();

                        $page = $response->getContent();

                        $sm = $e->getApplication()->getServiceManager();

                        $request = $sm->get('Request');

                        // Retrieve page cache
                        $cacheKey = 'cms_page_getter_'.$params['idpage'];
                        $cacheConfig = 'meliscms_page';
                        $melisEngineCacheSystem = $sm->get('MelisEngineCacheSystem');
                        $results = $melisEngineCacheSystem->getCacheByKey($cacheKey, $cacheConfig);
                        // Checking if the page is existing on page file cache
                        if (empty($results))
                        {
                            // Saving the page file cache
                            $melisEngineCacheSystem->setCacheByKey($cacheKey, $cacheConfig, $page);
                        }
                    }
                }

            }
        );
        $this->listeners[] = $callBackHandler;
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}