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
use Laminas\Session\Container;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener check if the Melis Page URL is correct.
 * If not, it will try to generate and redirect the correct one.
 * If ok, it will change the router to dispatch in the correct site module
 * and will add a few datas in the route to be used in the site.
 * 
 * Before redirecting in a module site, an event melisfront_site_dispatch_ready is launched, 
 * allowing other listener of plugins to update/modify the result of redirections, 404 or 
 * even the router's datas for dispatching in the site module.
 * Using this event is the correct way to interact with SEO.
 * 
 */
class MelisFrontDeletePluginCacheListener extends MelisGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
            '*', 
            [
                'meliscms_page_save_end',
                'meliscms_page_publish_end',
                'meliscms_page_unpublish_end',
                'meliscms_page_delete_end',
                'meliscms_page_move_end',
            ],
            function($e){

                $sm = $e->getTarget()->getServiceManager();

                $params = $e->getParams();

                if (!$params['success']) 
                    return;
                
                $melisEngineCacheSystem = $sm->get('MelisEngineCacheSystem');

                // Delete Menu plugin cached
                $cacheKey = 'MelisFrontMenuPlugin';
                $cacheConfig = 'melisfront_pages_file_cache';
                $melisEngineCacheSystem->deleteCacheByPrefix($cacheKey, $cacheConfig);

                // Delete Breadcrumb plugin cached
                $cacheKey = 'MelisFrontBreadcrumbPlugin';
                $cacheConfig = 'melisfront_pages_file_cache';
                $melisEngineCacheSystem->deleteCacheByPrefix($cacheKey, $cacheConfig);
            }
        );
    }
}