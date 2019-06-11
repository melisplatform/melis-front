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
 * This listener will override the home page id
 * of the site depending on the site domain.
 *
 * Since there is a possibility that one module
 * can have 1 or more sites and on module.config.php
 * file will allow only one ("/") route, this listener
 * will help to get the home page id of the site by
 * domain.
 */
class MelisFrontHomePageIdOverrideListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_ROUTE,
            function(MvcEvent $e){

                // Get route match to know if we are displaying in back or front
//                $routeMatch = $e->getRouteMatch();
                $sm = $e->getApplication()->getServiceManager();
//                $params = $routeMatch->getParams();

                // AssetManager, we don't want listener to be executed if it's not a php code
                $uri = $_SERVER['REQUEST_URI'];
                preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
                if (count($matches) > 1)
                    return;

                if (substr($uri, 0, 1) == '/')
                    $uri = substr($uri, 1, strlen($uri));

                /**
                 * Make sure that we are in home page
                 * :no page id
                 * :no lang locale (en/fr)
                 */
                if(empty($uri)) {
                    /**
                     * Get domain data
                     */
                    $domain = $_SERVER['SERVER_NAME'];
                    $melisTableDomain = $sm->get('MelisEngineTableSiteDomain');
                    $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain)->current();
                    $siteId = $datasDomain->sdom_site_id;
                    /**
                     * Get site data
                     */
                    $siteTable = $sm->get('MelisEngineTableSite');
                    $siteData = $siteTable->getEntryById($siteId)->current();

                    /**
                     * We override only the page id
                     * if the site lang option is set to
                     * default (1), because there is already
                     * a separate listener that handled
                     * the home page routes if the site lang option
                     * is set to 2.
                     *
                     * This Listener: MelisFrontHomePageRoutingListener
                     */
                    if($siteData->site_opt_lang_url == 1) {
                        $pageId = !empty($siteData->site_main_page_id) ? $siteData->site_main_page_id : null;
//                        $routeMatch->setParam('idpage', $pageId);

                        $router = $e->getRouter();
                        $request = $e->getRequest();
                        $routeM = $router->match($request);
                        //get the default params of the route
                        $params = $routeM->getParams();

                        //override the page id
                        $params['idpage'] = !empty($pageId) ? $pageId : $params['idpage'];

                        $route = Segment::factory(array(
                            'route' => '/',
                            'defaults' => $params,
                        ));

                        // add the route to the router
                        $router->addRoute($routeM->getMatchedRouteName(), $route);
                    }
                }
            }
        , 79);
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