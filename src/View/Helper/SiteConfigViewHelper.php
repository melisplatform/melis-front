<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;


class SiteConfigViewHelper extends AbstractHelper
{
    public $serviceManager;
    
    public function __construct($sm)
    {
        $this->serviceManager = $sm;
    }
    
    /**
     * This method return the site config
     * 
     * @param string $key
     * @param string $section
     * @param int $language
     * @return string
     */
    public function __invoke($key, $section = null, $language = null)
    {
        $siteConfigSrv = $this->serviceManager->get('MelisSiteConfigService');
        $config = $siteConfigSrv->getSiteConfigByKey($key, $section, $language);
        
        return $config;
    }
}