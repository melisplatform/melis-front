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
use MelisFront\Listener\MelisFrontAttachCssListener;
use MelisFront\Listener\MelisFrontSEOMetaPageListener;
use MelisFront\Listener\MelisFrontPluginsToLayoutListener;
use MelisFront\Listener\MelisFrontSEOReformatToRoutePageUrlListener;
use MelisFront\Listener\MelisFrontSEODispatchRouterRegularUrlListener;
use MelisFront\Listener\MelisFront404To301Listener;
use MelisFront\Listener\MelisFront404CatcherListener;
use MelisFront\Listener\MelisFrontXSSParameterListener;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
    	$eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm = $e->getApplication()->getServiceManager();
        $routeMatch = $sm->get('router')->match($sm->get('request'));

        $isBackOffice = false;

        if (!empty($routeMatch))
        {
            $routeName = $routeMatch->getMatchedRouteName();
            $module = explode('/', $routeName);

            if (!empty($module[0]))
            {
                if ($module[0] == 'melis-backoffice')
                {
                    $isBackOffice = true;
                }
            }
        }

        // do not load listeners if working on back-office
        if(!$isBackOffice) {
            // Catching PAGE SEO URLs to update Router
            $eventManager->attach(new MelisFrontSEOReformatToRoutePageUrlListener());

            // Adding css linked to the page
            $eventManager->attach(new MelisFrontAttachCssListener());

            // Adding different layout if displayed in front or front for melis back office
            $eventManager->attach(new MelisFrontLayoutListener());

            // Adding SEO meta datas to page
            $eventManager->attach(new MelisFrontSEOMetaPageListener());

            // Adding Plugins Ressources to page
            $eventManager->attach(new MelisFrontPluginsToLayoutListener());

            // Checking if Url is correct and redirect if not
            $eventManager->attach(new MelisFrontSEODispatchRouterRegularUrlListener());

            // Checking if Url is 404 and try to check if url has new Url
            $eventManager->attach($sm->get('MelisFront\Listener\MelisFront404To301Listener'));

            // This will try to look another url if 404 occured
            $eventManager->attach(new MelisFront404CatcherListener());

            // This will automatically prevent XSS attacks
            $eventManager->attach(new MelisFrontXSSParameterListener());
        }


        $this->createTranslations($e); 

        
        $container = new Container('melisplugins');
        $container['melis-plugins-lang-id'] = 1;
        $container['melis-plugins-lang-locale'] = 'en_EN';
    }
    
    public function createTranslations($e, $locale = 'en_EN')
    {
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator');
    
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];
        
        if (!empty($locale))
        {
            // Inteface translations
            $interfaceTransPath = 'module/MelisModuleConfig/languages/MelisFront/' . $locale . '.interface.php';
            $default = __DIR__ . '/../language/' . $locale . '.interface.php';
            
            $transPath = (file_exists($interfaceTransPath))? $interfaceTransPath : $default;
            
            $translator->addTranslationFile('phparray', $transPath);
        }
    }
    
    public function getConfig()
    {
    	$config = array();
    	$configFiles = array(
			include __DIR__ . '/../config/module.config.php',
    	    
    	    // Tests
			include __DIR__ . '/../config/diagnostic.config.php',
			include __DIR__ . '/../config/app.interface.php',

    	    // Templating Plugins
			include __DIR__ . '/../config/plugins/MelisFrontDragDropZonePlugin.config.php',
			include __DIR__ . '/../config/plugins/MelisFrontTagPlugin.config.php',
			include __DIR__ . '/../config/plugins/MelisFrontBreadcrumbPlugin.config.php',
			include __DIR__ . '/../config/plugins/MelisFrontMenuPlugin.config.php',
			include __DIR__ . '/../config/plugins/MelisFrontShowListFromFolderPlugin.config.php',
			include __DIR__ . '/../config/plugins/MelisFrontSearchResultsPlugin.config.php',

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
