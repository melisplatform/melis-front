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
use Zend\Session\Container;

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
class MelisFrontPluginLangSessionUpdateListener extends MelisFrontSEODispatchRouterAbstractListener
{
    public function attach(EventManagerInterface $events)
    {

        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_DISPATCH, 
        	function(MvcEvent $e){

                $idpage = null;
                $sm = $e->getApplication()->getServiceManager();
                $routeMatch = $e->getRouteMatch();
                $request = $e->getRequest();
                $postVal = $request->getPost();
                $idpage = $postVal['melisIdPage'];

                // If no id of page, then we're not in a melis page
                if (!empty($idpage))
                {

                    $renderMode = $routeMatch->getParam('renderMode');


                    // Getting the datas of the page depending on front or BO
                    if ($renderMode == 'melis')
                       $getmode = 'saved';
                    else
                       $getmode = 'published';

                    // Get page datas
                    $melisPage = $sm->get('MelisEnginePage');
                    $datasPage = $melisPage->getDatasPage($idpage, $getmode);
                    $container = new Container('melisplugins');
                    $container['melis-plugins-lang-id'] = $datasPage->getMelisPageTree()->plang_lang_id;
                    $container['melis-plugins-lang-locale'] = $datasPage->getMelisPageTree()->lang_cms_locale;
                }
            },
        99);
        
        $this->listeners[] = $callBackHandler;
    }
//    public function detach(EventManagerInterface $events)
//    {
//        foreach ($this->listeners as $index => $listener) {
//            if ($events->detach($listener)) {
//                unset($this->listeners[$index]);
//            }
//        }
//    }
}