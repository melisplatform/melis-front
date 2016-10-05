<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
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
use MelisFront\Listener\MelisFrontMetaPageSeoListener;
use MelisFront\Listener\MelisFrontReformatToRoutePageSeoUrlListener;
use MelisFront\Listener\MelisFrontPageRouterUpdateRegularUrlListener;

use MelisFront\Listener\MelisFrontPageRouterUpdateCommerceUrlListener;

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
        $eventManager->attach(new MelisFrontMetaPageSeoListener());

        // Catching PAGE SEO URLs to update Router
        $eventManager->attach(new MelisFrontReformatToRoutePageSeoUrlListener());
        
        // Checking if Url is correct and redirect if not
        $eventManager->attach(new MelisFrontPageRouterUpdateRegularUrlListener());
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
    						$renderMode = $routeMatch->getParam('renderMode');
    						$preview = $routeMatch->getParam('preview');
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
 
}
