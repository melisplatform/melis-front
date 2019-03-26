<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * This helper will translates
 */
class MelisSiteTranslateHelper extends AbstractHelper
{
    public $serviceManager;
    public $dataBreadcrumbs;
    
    public function __construct($sm)
    {
        $this->serviceManager = $sm;
        $this->dataBreadcrumbs = array();
    }
    
    /**
     * This method return translations
     * 
     * @param string $key
     * @param int $langId
     * @return string
     */
    public function __invoke($key, $langId)
    {
        $siteTransSrv = $this->serviceManager->get('MelisSiteTranslationService');
        
        $str = $siteTransSrv->getText($key, $langId);
        
        return $str;
    }
}