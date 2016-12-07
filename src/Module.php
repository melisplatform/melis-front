<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Stdlib\ArrayUtils;

use MelisFront\Listener\MelisFrontLayoutListener;
use MelisFront\Listener\MelisFrontSEOMetaPageListener;
use MelisFront\Listener\MelisFrontSEOReformatToRoutePageUrlListener;
use MelisFront\Listener\MelisFrontSEODispatchRouterRegularUrlListener;
use MelisFront\Listener\MelisFront404To301Listener;
use MelisFront\Listener\MelisFront404CatcherListener;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
    	$eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm = $e->getApplication()->getServiceManager();
        $routeMatch = $sm->get('router')->match($sm->get('request'));
        
        // Catching PAGE SEO URLs to update Router
        $eventManager->attach(new MelisFrontSEOReformatToRoutePageUrlListener());
        
        
        // Adding different layout if displayed in front or front for melis back office
        $eventManager->attach(new MelisFrontLayoutListener()); 
        
        // Adding SEO meta datas to page
        $eventManager->attach(new MelisFrontSEOMetaPageListener());
        
        // Checking if Url is correct and redirect if not
        $eventManager->attach(new MelisFrontSEODispatchRouterRegularUrlListener());
        
        // Checking if Url is 404 and try to check if url has new Url
        $eventManager->attach($sm->get('MelisFront\Listener\MelisFront404To301Listener'));
        
        // This will try to look another url if 404 occured
        $eventManager->attach(new MelisFront404CatcherListener());
    }

    public function getConfig()
    {
    	$config = array();
    	$configFiles = array(
    			include __DIR__ . '/../config/module.config.php',
    	);
    	
    	foreach ($configFiles as $file) {
    		$config = ArrayUtils::merge($config, $file);
    	} 
    	
    	return $config;
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
 
}
