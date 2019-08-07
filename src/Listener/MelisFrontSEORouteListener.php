<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\Router\Http\Segment;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;


class MelisFrontSEORouteListener
{
    public function onLoadModulesPost(ModuleEvent $e)
    {
        /** @var ServiceManager $sm */
        $sm = $e->getParam('ServiceManager');
        if(!empty($_SERVER['REQUEST_URI'])){
            $uri = $_SERVER['REQUEST_URI'];

            //we don't want listener to be executed if it's not a php code
            preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 1)
                return;

            //check if we are in front
            if ($uri != 'melis')
            {
                // get request
                $request = $sm->get('request');
                // get uri
                $uri = $request->getUri();
                $url = $uri->getPath();
                // remove slash on first
                $url = preg_replace('/\//i',null,$url,1);
                // get the url parameters
                $urlParams = $request->getQuery()->toString();
                if (! empty($urlParams)) {
                    $urlParams = '?' . $urlParams;
                }
                // Trying to find an URL in Melis SEO
                $melisTablePageSeo = $sm->get('MelisEngineTablePageSeo');
                $datasPageSeo = $melisTablePageSeo->getEntryByField('pseo_url', $url);
                if (!empty($datasPageSeo)) {
                    // get page seo data
                    $datasPageSeo = $datasPageSeo->current();
                    if (!empty($datasPageSeo)) {
                        // get router
                        $router = $sm->get('router');
                        // Creating dynamicaly the route and the params that are needed in the regular melis routing
                        $route = Segment::factory(array(
                            'route' => '/' . $url,
                            'defaults' => array(
                                'controller' => 'MelisFront\Controller\Index',
                                'action' => 'index',
                                'idpage' => $datasPageSeo->pseo_id,
                                'renderType' => 'melis_zf2_mvc',
                                'renderMode' => 'front',
                                'preview' => false,
                                'urlparams' => $urlParams,
                            )
                        ));
                        // add the route to the router
                        $router->addRoute('melis-front-page-seo', $route);
                    }
                }
            }
        }
    }
}