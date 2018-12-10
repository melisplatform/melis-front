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
use MelisFront\Listener\MelisFrontPageCacheListener;

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

        $container = new Container('melisplugins');
        $container['melis-plugins-lang-id'] = 1;
        $container['melis-plugins-lang-locale'] = 'en_EN';

        if (!empty($routeMatch))
        {

            $this->createTranslations($e, $routeMatch);

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
        if(!$isBackOffice)
        {
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

            $eventManager->attach(new MelisFrontPageCacheListener());
        }
    }

    public function createTranslations($e, $routeMatch)
    {
        $container = new Container('melisplugins');
        $locale = $container['melis-plugins-lang-locale'];

        $param = $routeMatch->getParams();

        // Checking if the Request is from Melis-BackOffice or Front
        if (!empty($param['renderMode']))
        {
            if ($param['renderMode'] == 'melis')
            {
                $container = new Container('meliscore');
                $locale = $container['melis-lang-locale'];
            }
            else
            {
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
        }
        else
        {
            if (!empty($param['action']))
            {
                // MelisCore locale will be use translations in plugin modals requests
                if (in_array($param['action'], array('renderPluginModal', 'validatePluginModal')))
                {
                    $container = new Container('meliscore');
                    $locale = $container['melis-lang-locale'];
                }
            }
        }

        if (!empty($locale))
        {
            // Inteface translations
            $interfaceTransPath = 'module/MelisModuleConfig/languages/MelisFront/' . $locale . '.interface.php';
            $default = __DIR__ . '/../language/' . $locale . '.interface.php';

            $transPath = (file_exists($interfaceTransPath))? $interfaceTransPath : $default;

            $sm = $e->getApplication()->getServiceManager();
            $translator = $sm->get('translator');
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
            include __DIR__ . '/../config/plugins/MelisFrontBlockSectionPlugin.config.php',

        );

        foreach ($configFiles as $file) {
            $config = ArrayUtils::merge($config, $file);
        }

        if(!empty($this->prepareMiniTemplateConfig())){
            $config = ArrayUtils::merge($config, $this->prepareMiniTemplateConfig());
        }

        return $config;
    }

    /**
     * Function to prepare the Mini Template config
     *
     * @return array
     */
    public function prepareMiniTemplateConfig()
    {
        $pluginsFormat = array();
        $userSites = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites';
        if(file_exists($userSites) && is_dir($userSites)) {
            $sites = $this->getDir($userSites);
            if(!empty($sites)){
                foreach($sites as $key => $val) {
                    //public site folder
                    $publicFolder = $userSites . DIRECTORY_SEPARATOR . $val . DIRECTORY_SEPARATOR . 'public';
                    //mini template image folder
//                    $imgFolder = $publicFolder . DIRECTORY_SEPARATOR . 'images' .DIRECTORY_SEPARATOR . 'miniTemplate';
                    //get the mini template folder path
                    $miniTplPath = $publicFolder . DIRECTORY_SEPARATOR . 'miniTemplatesTinyMce';
                    //check if directory is available
                    if(file_exists($miniTplPath) && is_dir($miniTplPath)) {
                        //get the plugin config format
                        $pluginsConfig = include __DIR__ . '/../config/plugins/MiniTemplatePlugin.config.php';
                        if(!empty($pluginsConfig)) {
                            //get all the mini template
                            $tpls = array_diff(scandir($miniTplPath), array('..', '.'));
                            if (!empty($tpls)) {
                                //set the site name as sub category title
                                $pluginsConfig['melis']['subcategory']['title'] = $val;
                                //set the id of the plugin
                                $pluginsConfig['melis']['subcategory']['id'] = $pluginsConfig['melis']['subcategory']['id'] . '_' . $val;
                                //get the content of the mini template
                                foreach ($tpls as $k => $v) {
                                    //remove the file extension from the filename
                                    $name = pathinfo($v, PATHINFO_FILENAME);
                                    //create a plugin post name
                                    $postName = $k . strtolower($name);
                                    //prepare the content of the mini template
                                    $content = $miniTplPath . DIRECTORY_SEPARATOR . $v;
                                    //set the default layout for the plugin based on mini template
                                    $pluginsConfig['front']['default'] = file_get_contents($content);
                                    //set the plugin name using the template name
                                    $pluginsConfig['melis']['name'] = $name;
                                    //include the mini tpl plugin config
                                    $pluginsFormat['plugins']['MelisMiniTemplate']['plugins']['MiniTemplatePlugin_' . $postName] = $pluginsConfig;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $pluginsFormat;
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

    /**
     * Returns all the sub-folders in the provided path
     * @param String $dir
     * @param array $excludeSubFolders
     * @return array
     */
    protected function getDir($dir, $excludeSubFolders = array())
    {
        $directories = array();
        if(file_exists($dir)) {
            $excludeDir = array_merge(array('.', '..', '.gitignore'), $excludeSubFolders);
            $directory  = array_diff(scandir($dir), $excludeDir);

            foreach($directory as $d) {
                if(is_dir($dir.'/'.$d)) {
                    $directories[] = $d;
                }
            }

        }
        return $directories;
    }

}
