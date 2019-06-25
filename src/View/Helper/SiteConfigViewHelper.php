<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use MelisFront\Service\MelisSiteConfigService;
use Zend\Http\Request;
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

        $pageId = null;

        if (!empty($params['idpage']))
            $pageId = $params['idpage'];
        //if page id is still empty, try to get it on the post
        $postVal = $request->getPost();
        if(empty($pageId) && !empty($postVal['idpage'])){
            $pageId = $postVal['idpage'];
        }
        if(empty($pageId) && !empty($postVal['pageId'])){
            $pageId = $postVal['pageId'];
        }
        /**
         * if page id is still empty,
         * try to get it from the global $_GET & _POST variable
         * with pageId as variable name
         */
        if(empty($pageId)){
            if(!empty($_GET['pageId']))
                $pageId = $_GET['pageId'];
            if(!empty($_POST['pageId']) && empty($pageId))
                $pageId = $_POST['pageId'];
        }

        /** @var MelisSiteConfigService $siteConfigSrv */
        $siteConfigSrv = $this->serviceManager->get('MelisSiteConfigService');
        $config = $siteConfigSrv->getSiteConfigByKey($key, $pageId, $section, $language);
        
        return $config;
    }
}