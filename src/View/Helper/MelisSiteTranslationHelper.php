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
    
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
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
        
        $str = $siteTransSrv->getText($key, $langId, $siteId);
        
        return $str;
    }
}