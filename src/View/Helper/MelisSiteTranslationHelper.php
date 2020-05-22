<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;


class MelisSiteTranslationHelper extends AbstractHelper
{
    public $serviceManager;
    
    public function __construct($sm)
    {
        $this->serviceManager = $sm;
    }
    
    /**
     * This method return translations
     * 
     * @param string $key
     * @param int $langId
     * @param int $siteId
     * @return string
     */
    public function __invoke($key, $langId, $siteId)
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
        
        return $str;
    }
}