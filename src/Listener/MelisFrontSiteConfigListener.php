<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\ModuleManager\ModuleEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;


class MelisFrontSiteConfigListener
{
    public function onLoadModulesPost(ModuleEvent $e)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $e->getParam('ServiceManager');

        if(!empty($_SERVER['REQUEST_URI'])){
            $uri = $_SERVER['REQUEST_URI'];

            //we don't want listener to be executed if it's not a php code
            preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 1)
                return;

            $uri1 = '';
            $tabUri = explode('/', $uri);
            if (!empty($tabUri[1]))
                $uri1 = $tabUri[1];

            //check if we are in front
            if ($uri1 != 'melis')
            {
                $pageId = null;
                // get page id
                if ($tabUri[1] == 'id')
                    $pageId == $tabUri[2];
                else if ($tabUri[2] == 'id')
                    $pageId == $tabUri[3];
                //get the config listener
                $configListener = $e->getConfigListener();
                //get the merged config
                $config         = $configListener->getMergedConfig(false);

                /**
                 * get domain
                 */
                $domain = $_SERVER['SERVER_NAME'];
                /**
                 * get site data
                 */
                $siteService = $serviceManager->get('MelisTreeService');
                $siteData = $siteService->getSiteByPageId($pageId);

                if(!empty($siteData)) {
                    $siteId = $siteData->site_id;
                    /**
                     * get the site config
                     */
                    $siteConfig = $serviceManager->get('MelisSiteConfigService');
                    $siteConfig = $siteConfig->getSiteConfig($siteId, true);
                    $config = ArrayUtils::merge($config, $siteConfig, true);
                    /**
                     * remove other site data from the config
                     */
                    if (!empty($config['site'][$siteData->site_name])) {
                        foreach ($config['site'][$siteData->site_name] as $id => $site) {
                            if ($id != $siteId && $id != 'allSites') {
                                if (is_int($id))
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
                $serviceManager->setService('config', $config);
                $serviceManager->setAllowOverride(false);
            }
        }
    }
}