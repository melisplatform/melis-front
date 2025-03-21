<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;


class MelisSiteTranslationHelper extends AbstractHelper
{
    public $serviceManager;
    
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
    /**
     * This method return translations
     * 
     * @param string $key
     * @param int $langId
     * @param int $siteId
     * @param boolean $isInAttribute - check if the translations is put inside an attribute like placeholder or title
     * @return string
     */
    public function __invoke($key, $langId, $siteId, $isInAttribute = false)
    {
        $siteTransSrv = $this->serviceManager->get('MelisSiteTranslationService');

        /**
         * Try to get the translation from the cache
         */
        $cacheData = $siteTransSrv->getCachedTranslations($siteId);
        if(empty($cacheData)){
            //generate cache for translation
            $siteTransSrv->cacheTranslations($siteId);
            //get the data again from cache
            $cacheData = $siteTransSrv->getCachedTranslations($siteId);
        }
        $str = $cacheData[$langId][$key] ?? $key;

        /**
         * We modify only the translation if we are not inside
         * an attribute since html attribute don't accept
         * html tag like span
         */
        if(!$isInAttribute) {
            if (!empty($this->getView()->renderMode)) {
                if ($this->getView()->renderMode != 'front') {
                    //return translation with a span so they can see the key when they hover the text
                    $str = '<span title="' . $key . '">' . $str . '</span>';
                }
            } else {//try to check uri if we are in BO
                $uri = $_SERVER['REQUEST_URI'];
                $uri = explode('/', $uri);
                if (in_array('renderMode', $uri)) {
                    //return translation with a span so they can see the key when they hover the text
                    $str = '<span title="' . $key . '">' . $str . '</span>';
                }
            }
        }

        return $str;
    }
}