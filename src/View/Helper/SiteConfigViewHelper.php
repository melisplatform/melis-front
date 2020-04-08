<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use MelisFront\Service\MelisSiteConfigService;
use Laminas\Http\Request;
use Laminas\View\Helper\AbstractHelper;


class SiteConfigViewHelper extends AbstractHelper
{
    public $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
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
         * if page id is still empty, try on get
         */
        $getValue = $request->getQuery();
        if(empty($pageId) && !empty($getValue['idpage'])){
            $pageId = $getValue['idpage'];
        }
        if(empty($pageId) && !empty($getValue['pageId'])){
            $pageId = $getValue['pageId'];
        }

        /** @var MelisSiteConfigService $siteConfigSrv */
        $siteConfigSrv = $this->serviceManager->get('MelisSiteConfigService');
        $config = $siteConfigSrv->getSiteConfigByKey($key, $pageId, $section, $language);
        
        return $config;
    }
}