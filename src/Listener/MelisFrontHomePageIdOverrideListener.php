<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

/**
 * This listener will override the home page id
 * of the site depending on the site domain.
 *
 * Since there is a possibility that one module
 * can have 1 or more sites and on module.config.php
 * file will allow only one ("/") route, this listener
 * will help to get the home page id of the site by
 * domain.
 */
class MelisFrontHomePageIdOverrideListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            function(MvcEvent $e){

                // Get route match to know if we are displaying in back or front
                $routeMatch = $e->getRouteMatch();
                $sm = $e->getApplication()->getServiceManager();
                $params = $routeMatch->getParams();

                /**
                 * Check if we are on front
                 */
                if($params['renderMode'] == 'front') {
                    if ($routeMatch) {
                        /**
                         * Get domain data
                         */
                        $domain = $_SERVER['SERVER_NAME'];
                        $melisTableDomain = $sm->get('MelisEngineTableSiteDomain');
                        $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain)->current();
                        $siteId = $datasDomain->sdom_site_id;
                        /**
                         * Get site data
                         */
                        $siteTable = $sm->get('MelisEngineTableSite');
                        $siteData = $siteTable->getEntryById($siteId)->current();

                        /**
                         * We override only the page id
                         * if the site lang option is set to
                         * default (1), because there is already
                         * a separate listener that handled
                         * the home page routes if the site lang option
                         * is set to 2.
                         *
                         * This Listener: MelisFrontHomePageRoutingListener
                         */
                        if($siteData->site_opt_lang_url == 1) {
                            $uri = $_SERVER['REQUEST_URI'];
                            if (substr($uri, 0, 1) == '/')
                                $uri = substr($uri, 1, strlen($uri));

                            /**
                             * Make sure that we are in home page
                             * :no page id
                             * :no lang locale (en/fr)
                             */
                            if(empty($uri)) {
                                $siteId = !empty($siteData->site_main_page_id) ? $siteData->site_main_page_id : $params['idpage'];
                                $routeMatch->setParam('idpage', $siteId);
                            }
                        }
                    }
                }
            }
        , 1000);
        $this->listeners[] = $callBackHandler;
    }

    /**
     * Function to create css link
     *
     * @param $content
     * @param $cssToAdd
     * @return null|string|string[]
     */
    private function createCssLink($content, $cssToAdd)
    {
        $headRegex = '/(<\/head>)/im';
        $newContent = preg_replace($headRegex, "$cssToAdd$1", $content);
        return $newContent;
    }

    /**
     * @param $content
     * @param $cssToAdd
     * @param $jsToAdd
     * @return null|string|string[]
     */
    private function createLink($content, $cssToAdd, $jsToAdd)
    {
        $content = $this->createCssLink($content, $cssToAdd);
        $content = $this->createJsLink($content, $jsToAdd);
        return $content;
    }

    /**
     * Function to create js link
     *
     * @param $content
     * @param $jsToAdd
     * @return null|string|string[]
     */
    private function createJsLink($content, $jsToAdd)
    {
//        $bodyRegex = '/(<\/body>)/im';
        /**
         * This will load just under the last
         * </div> of the page instead of before
         * the </body> because there are some
         * scripts are directly attached to the view
         * from the controller
         */
        $bodyRegex = '/(<\/div>(?![\s\S]*<\/div>[\s\S]*$))/im';
        $newContent = preg_replace($bodyRegex, "$1$jsToAdd", $content);
        return $newContent;
    }

    /**
     * Function to get all the assets for the
     * config to load if the bundle does'nt exist
     *
     * @param $content
     * @param $dir
     * @param null $type
     * @param bool $isFromVendor
     * @param string $siteName
     * @return string
     */
    private function loadAssetsFromConfig($content, $dir, $type = null, $isFromVendor = false, $siteName = '')
    {
        $newContent = $content;
        $assetsConfig = $dir.'assets.config.php';
        /**
         * check if the config exist
         */
        if (file_exists($assetsConfig)) {
            $files = include($assetsConfig);
            /**
             * check if assets config is not empty
             */
            if (!empty($files)) {
                foreach($files as $key => $file){
                    /**
                     * check if type to know what asset are
                     * we going to load
                     */
                    if(empty($type)) {
                        /**
                         * this will load the assets from the config
                         */
                        $cssToAdd = "\n";
                        $jsToLoad = "\n";
                        if (strtolower($key) == 'css') {
                            foreach ($file as $k => $css) {
                                $css = str_replace('/public', '', $css);
                                $css = $this->editFileName($css, $isFromVendor, $siteName);
                                $cssToAdd .= '<link href="' . $css . '" media="screen" rel="stylesheet" type="text/css">' . "\n";
                            }
                        }
                        elseif (strtolower($key) == 'js') {
                            foreach ($file as $k => $js) {
                                $js = str_replace('/public', '', $js);
                                $js = $this->editFileName($js, $isFromVendor, $siteName);
                                $jsToLoad .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";
                            }
                        }
                        $newContent = $this->createLink($newContent, $cssToAdd, $jsToLoad);
                    }
                    elseif($type == 'css'){
                        /**
                         * this will load only the css
                         * from the config
                         */
                        if (strtolower($key) == 'css') {
                            $cssToAdd = "\n";
                            foreach ($file as $k => $css) {
                                $css = str_replace('/public', '', $css);
                                $css = $this->editFileName($css, $isFromVendor, $siteName);
                                $cssToAdd .= '<link href="' . $css . '" media="screen" rel="stylesheet" type="text/css">' . "\n";
                            }
                            $newContent = $this->createCssLink($content, $cssToAdd);
                        }
                    }elseif($type == 'js'){
                        /**
                         * this will load the js only from the config
                         */
                        if (strtolower($key) == 'js') {
                            $jsToLoad = "\n";
                            foreach ($file as $k => $js) {
                                $js = str_replace('/public', '', $js);
                                $js = $this->editFileName($js, $isFromVendor, $siteName);
                                $jsToLoad .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";
                            }
                            $newContent = $this->createJsLink($content, $jsToLoad);
                        }
                    }
                }
            }
        }
        return $newContent;
    }

    /**
     * Edit the filename only if site came from vendor
     * to make the module name camel case
     * Ex. melis-demo-cms turns into MelisDemoCms
     *
     * @param $fileName
     * @param $isFromVendor
     * @param $siteName
     * @return string|string[]|null
     */
    private function editFileName($fileName, $isFromVendor, $siteName){
        if($isFromVendor){
            $pathInfo = explode('/', $fileName);
            for($i = 0; $i <= sizeof($pathInfo); $i++){
                if(!empty($pathInfo[1])){
                    if(str_replace('-', '', ucwords($pathInfo[1], '-')) == $siteName){
                        $fileName = preg_replace('/'.$pathInfo[1].'/', $siteName, $fileName, 1);
                    }
                }
            }
        }
        return $fileName;
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}