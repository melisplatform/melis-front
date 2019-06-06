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
    public $request;
    
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
        $pageId = null;

        /**
         * access the router to get the
         * page id
         */
        $router = $this->serviceManager->get('router');
        $request = $this->serviceManager->get('request');
        $routeMatch = $router->match($request);
        $params = $routeMatch->getParams();

        /**
         * check page id
         */
        if(!empty($params['idpage'])){
            $pageId = $params['idpage'];
        }else{
            $pageId = (!empty($_GET['pageId'])) ? $_GET['pageId'] : $_POST['pageId'];
        }
        /** @var MelisSiteConfigService $siteConfigSrv */
        $siteConfigSrv = $this->serviceManager->get('MelisSiteConfigService');
        $config = $siteConfigSrv->getSiteConfigByKey($key, $pageId, $section, $language);
        
        return $config;
    }
}