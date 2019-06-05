<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use MelisFront\Service\MelisSiteConfigService;
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
        /**
         * access the router to get the
         * page id
         */
        $router = $this->serviceManager->get('router');
        $request = $this->serviceManager->get('request');
        $routeMatch = $router->match($request);
        $params = $routeMatch->getParams();

        /** @var MelisSiteConfigService $siteConfigSrv */
        $siteConfigSrv = $this->serviceManager->get('MelisSiteConfigService');
        $config = $siteConfigSrv->getSiteConfigByKey($key, $params['idpage'], $section, $language);
        
        return $config;
    }
}