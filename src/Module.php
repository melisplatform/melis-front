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

use MelisFront\View\Helper\MelisTagsHelper;
use MelisFront\View\Helper\MelisLinksHelper;

use MelisFront\Listener\MelisFrontLayoutListener;
use MelisFront\Listener\MelisFrontSEOMetaPageListener;
use MelisFront\Listener\MelisFrontSEOReformatToRoutePageUrlListener;
use MelisFront\Listener\MelisFrontSEODispatchRouterRegularUrlListener;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
    	$eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        
        // Adding different layout if displayed in front or front for melis back office
        $eventManager->attach(new MelisFrontLayoutListener()); 
        
        // Adding SEO meta datas to page
        $eventManager->attach(new MelisFrontSEOMetaPageListener());

        // Catching PAGE SEO URLs to update Router
        $eventManager->attach(new MelisFrontSEOReformatToRoutePageUrlListener());
        
        // Checking if Url is correct and redirect if not
        $eventManager->attach(new MelisFrontSEODispatchRouterRegularUrlListener());
    }
    
    
    public function init(ModuleManager $manager)
    {
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
    
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'MelisFront\Service\MelisFrontHeadService' =>  function($sm) {
    						$melisMelisFrontHeadService = new \MelisFront\Service\MelisFrontHeadService();
    						$melisMelisFrontHeadService->setServiceLocator($sm);
    						return $melisMelisFrontHeadService;
    					},
    					'MelisFrontNavigation' =>  function($sm) {
    						$router = $sm->get('router');
    						$request = $sm->get('request');
    						$routeMatch = $router->match($request);
    						if ($routeMatch)
    						{
    							$params = $routeMatch->getParams();
    							$idpage = $params['idpage'];
    							$renderMode = $params['renderMode'];
    							
    							$navigation = new \MelisFront\Navigation\MelisFrontNavigation($sm, $idpage, $renderMode);
    							$navigationService =  $navigation->createService($sm);
    						}
    						else
    						{
    							$navigation = new \MelisFront\Navigation\MelisFrontNavigation($sm, 0, 'front');
    							$navigationService =  $navigation->createService($sm);
    						}
    						return $navigationService;
    					},
    			)
    	);
    }

    public function getViewHelperConfig()
    {
    	return array(
    			'factories' => array(
    					'MelisTag' => function($sm) {
    						$sl = $sm->getServiceLocator();
    						$router = $sm->getServiceLocator()->get('router');
    						$request = $sm->getServiceLocator()->get('request');
    						$routeMatch = $router->match($request);
    						
    						if (!empty($routeMatch))
    						{
    						    $renderMode = $routeMatch->getParam('renderMode');
    						    $preview = $routeMatch->getParam('preview');
    						}
    						else
    						{
    						    $renderMode = 'front';
    						    $preview = false;
    						}
    						$helper = new MelisTagsHelper($sl, $renderMode, $preview);
    						return $helper;
    					},
    					'MelisLink' => function($sm) {
    						$sl = $sm->getServiceLocator();
    						$helper = new MelisLinksHelper($sl);
    						return $helper;
    					}
    			)
    	);
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
