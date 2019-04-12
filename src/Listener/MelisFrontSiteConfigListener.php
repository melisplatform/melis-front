<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;


class MelisFrontSiteConfigListener
{
    public function onLoadModulesPost(ModuleEvent $e)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $e->getParam('ServiceManager');

        if(!empty($_SERVER['REQUEST_URI'])){
            $uri = $_SERVER['REQUEST_URI'];
            $uri1 = '';
            $tabUri = explode('/', $uri);
            if (!empty($tabUri[1]))
                $uri1 = $tabUri[1];

            //check if we are in front
            if ($uri1 != 'melis')
            {
                //get the config listener
                $configListener = $e->getConfigListener();
                //get the merged config
                $config         = $configListener->getMergedConfig(false);

                /**
                 * get the site id via domain
                 */
                $domain = $_SERVER['SERVER_NAME'];
                $melisTableDomain = $serviceManager->get('MelisEngineTableSiteDomain');
                $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain)->current();
                if(!empty($datasDomain)){
                    $siteId = $datasDomain->sdom_site_id;
                    /**
                     * get site name
                     */
                    $siteTable = $serviceManager->get('MelisEngineTableSite');
                    $siteData = $siteTable->getEntryById($siteId)->current();
                    /**
                     * get the site config
                     */
                    $siteConfig = $serviceManager->get('MelisSiteConfigService');
                    $siteConfig = $siteConfig->getSiteConfig($siteId, true);
                    $config = ArrayUtils::merge($config, $siteConfig, true);
                    /**
                     * remove other site data from the config
                     */
                    if(!empty($config['site'][$siteData->site_name])){
                        foreach($config['site'][$siteData->site_name] as $id => $site){
                            if($id != $siteId && $id != 'allSites'){
                                unset($config['site'][$siteData->site_name][$id]);
                            }
                        }
                    }
                }

                // Pass the changed configuration back to the listener:
                $configListener->setMergedConfig($config);
                $e->setConfigListener($configListener);
                /**
                 * Update the config inside the service
                 */
                $serviceManager->setAllowOverride(true);
                $serviceManager->setService('Config', $config);
                $serviceManager->setAllowOverride(false);
            }
        }
    }
}