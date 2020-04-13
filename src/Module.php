<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront;

use MelisFront\Listener\MelisFrontHomePageIdOverrideListener;
use MelisFront\Listener\MelisFrontHomePageRoutingListener;
use MelisFront\Listener\MelisFrontMinifiedAssetsCheckerListener;
use MelisFront\Listener\MelisFrontMiniTemplateConfigListener;
use MelisFront\Listener\MelisFrontPluginLangSessionUpdateListener;
use MelisFront\Listener\MelisFrontSEORouteListener;
use MelisFront\Listener\MelisFrontSiteConfigListener;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\ServiceManager;
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
use MelisFront\Listener\MelisFrontPageCacheListener;

class Module
{
    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        // Registering a listener at default priority, 1, which will trigger
        // after the ConfigListener merges config.
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, [new MelisFrontMiniTemplateConfigListener(), 'onLoadModulesPost']);
        /**
         * get the site config (merged with db)
         */
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, [new MelisFrontSiteConfigListener(), 'onLoadModulesPost']);
        /**
         *  - Catching PAGE SEO URLs to update Router
         *    > create SEO route first so the modules can have a route match in creating translations
         */
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, [new MelisFrontSEORouteListener(), 'onLoadModulesPost']);
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm = $e->getApplication()->getServiceManager();
        $routeMatch = $sm->get('router')->match($sm->get('request'));

        $isBackOffice = false;


        if (!empty($routeMatch)) {

            $this->createTranslations($e, $routeMatch);

            $routeName = $routeMatch->getMatchedRouteName();
            $module = explode('/', $routeName);

            if (!empty($module[0])) {
                if ($module[0] == 'melis-backoffice') {
                    $isBackOffice = true;
                }
            }
        }


        // do not load listeners if working on back-office
        if(!$isBackOffice) {
            // Catching PAGE SEO URLs to update Router
            //$eventManager->attach(new MelisFrontSEOReformatToRoutePageUrlListener()); -> refer init() Listener: MelisFrontSEORouteListener, issueL no translations of melis-modules

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

            $eventManager->attach(new MelisFrontPageCacheListener());

            $eventManager->attach(new MelisFrontMinifiedAssetsCheckerListener());

            $eventManager->attach(new MelisFrontHomePageRoutingListener());

            $eventManager->attach(new MelisFrontHomePageIdOverrideListener());

        } else {
            $eventManager->attach(new MelisFrontPluginLangSessionUpdateListener());
        }
    }

    public function createTranslations($e, $routeMatch)
    {
        $container = new Container('melisplugins');
        $locale = $container['melis-plugins-lang-locale'];

        $container = new Container('meliscore');
        if (empty($locale)) {
            $locale = $container['melis-lang-locale'];
        }

        // Checking if the Request is from Melis-BackOffice or Front
        $param = $routeMatch->getParams();
        if (!empty($param['renderMode'])) {
            if ($param['renderMode'] == 'melis') {
                $locale = $container['melis-lang-locale'];
            } else {
                // Session language for front
                if (!empty($param['idpage']) || !empty($param['frontIdpage'])) {
                    $idpage = !empty($param['idpage']) ? $param['idpage'] : $param['frontIdpage'];
                    $sm = $e->getApplication()->getServiceManager();
                    $melisPagelangTbl = $sm->get('MelisEngine\Model\Tables\MelisPageLangTable');
                    $currentPage = $melisPagelangTbl->getEntryByField('plang_page_id', $idpage)->current();
                    $melisCmsLang = $sm->get('MelisEngine\Model\Tables\MelisCmsLangTable');
                    $currentPageLang = $melisCmsLang->getEntryById($currentPage->plang_lang_id)->current();
                    if (!empty($currentPageLang)) {
                        $container = new Container('melisplugins');
                        $container['melis-plugins-lang-id'] = $currentPageLang->lang_cms_id;
                        $container['melis-plugins-lang-locale'] = $currentPageLang->lang_cms_locale;
                        $locale = $currentPageLang->lang_cms_locale;
                    }
                }
            }
        } else {
            if (!empty($param['action'])) {
                // MelisCore locale will be use translations in plugin modals requests
                if (in_array($param['action'], ['renderPluginModal', 'validatePluginModal'])) {
                    $container = new Container('meliscore');
                    $locale = $container['melis-lang-locale'];
                }
            }
        }

        if (!empty($locale)) {
            // Inteface translations
            $interfaceTransPath = 'module/MelisModuleConfig/languages/MelisFront/' . $locale . '.interface.php';
            $default = __DIR__ . '/../language/' . $locale . '.interface.php';

            $transPath = (file_exists($interfaceTransPath))? $interfaceTransPath : $default;

            $sm = $e->getApplication()->getServiceManager();
            $translator = $sm->get('translator');
            if (file_exists($transPath)) {
                $translator->addTranslationFile('phparray', $transPath);
            }
        }
    }

    public function getConfig()
    {
        $config = [];
        $configFiles = [
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
            include __DIR__ . '/../config/plugins/MelisFrontBlockSectionPlugin.config.php',
            include __DIR__ . '/../config/plugins/MelisFrontGdprBannerPlugin.config.php',
            include __DIR__ . '/../config/plugins/MelisFrontGdprRevalidationPlugin.config.php',
        ];


        foreach ($configFiles as $file) {
            $config = ArrayUtils::merge($config, $file);
        }

        return $config;
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
