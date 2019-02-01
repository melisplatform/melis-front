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
use Zend\Mvc\Router\Http\Segment;

/**
 * Minified Assets Checker listener
 */
class MelisFrontMinifiedAssetsCheckerListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_FINISH,
            function(MvcEvent $e){
                
                // Get route match to know if we are displaying in back or front
                $routeMatch = $e->getRouteMatch();

                if($routeMatch) {

                    $params = $routeMatch->getParams();

                    if ($params['renderMode'] == 'front')
                    {
                        $response = $e->getResponse();
                        $content = $response->getContent();
                        $newContent = $content;

                        $cssBundleLoaded = false;
                        $jsBundleLoaded = false;

                        $siteDir = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/'.$params['module'].'/';
                        $publicDir = $siteDir.'public/';
                        $configDir = $siteDir.'config/';
                        /**
                         * check if bundle.css is exist
                         */
                        if(file_exists($publicDir.'bundle.css')){
                            /**
                             * load the bundle.css  if it exist
                             */
                            $cssName = '/'.$params['module'].'/bundle.css';
                            $cssToAdd = "\n";
                            $cssToAdd .= '<link href="' . $cssName . '" media="screen" rel="stylesheet" type="text/css">' . "\n";
                            $newContent = $this->createCssLink($newContent, $cssToAdd);
                            $cssBundleLoaded = true;
                        }

                        /**
                         * check if bundle.js is exist
                         */
                        if(file_exists($publicDir.'bundle.js')){
                            //load the bundle
                            $jsName = '/'.$params['module'].'/bundle.js';
                            $jsToLoad = "\n";
                            $jsToLoad .= '<script type="text/javascript" src="' . $jsName . '"></script>' . "\n";
                            $newContent = $this->createJsLink($newContent, $jsToLoad);
                            $jsBundleLoaded = true;
                        }

                        /**
                         * This will going to check whether we are going
                         * to load all the assets from the config or only
                         * the css or js.
                         */
                        if($cssBundleLoaded && !$jsBundleLoaded){
                            $newContent = $this->loadAssetsFromConfig($newContent, $configDir, 'js');
                        }elseif(!$cssBundleLoaded && $jsBundleLoaded){
                            $newContent = $this->loadAssetsFromConfig($newContent, $configDir, 'css');
                        }elseif(!$cssBundleLoaded && !$jsBundleLoaded){
                            $newContent = $this->loadAssetsFromConfig($newContent, $configDir);
                        }

                        /**
                         * append all the assets to the header or
                         * to the body of the page
                         */
                        $response->setContent($newContent);
                    }
                }

            }
        , 116);
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
     * Function to create js link
     *
     * @param $content
     * @param $jsToLoad
     * @return null|string|string[]
     */
    private function createJsLink($content, $jsToLoad)
    {
        $bodyRegex = '/(<\/body>)/im';
        $newContent = preg_replace($bodyRegex, "$jsToLoad$1", $content);
        return $newContent;
    }

    /**
     * Function to get all the assets for the
     * config to load if the bundle does'nt exist
     *
     * @param $content
     * @param $dir
     * @param null $type
     * @return string
     */
    private function loadAssetsFromConfig($content, $dir, $type = null)
    {
        $newContent = '';
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
                        if (strtolower($key) == 'css') {
                            $cssToAdd = "\n";
                            foreach ($file as $k => $css) {
                                $css = str_replace('/public', '', $css);
                                $cssToAdd .= '<link href="' . $css . '" media="screen" rel="stylesheet" type="text/css">' . "\n";
                            }
                            $newContent .= $this->createCssLink($content, $cssToAdd);
                        }
                        elseif (strtolower($key) == 'js') {
                            $jsToLoad = "\n";
                            foreach ($file as $k => $js) {
                                $js = str_replace('/public', '', $js);
                                $jsToLoad .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";
                            }
                            $newContent .= $this->createJsLink($content, $jsToLoad);
                        }
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
                                $cssToAdd .= '<link href="' . $css . '" media="screen" rel="stylesheet" type="text/css">' . "\n";
                            }
                            $newContent .= $this->createCssLink($content, $cssToAdd);
                        }
                    }elseif($type == 'js'){
                        /**
                         * this will load the js only from the config
                         */
                        if (strtolower($key) == 'js') {
                            $jsToLoad = "\n";
                            foreach ($file as $k => $js) {
                                $js = str_replace('/public', '', $js);
                                $jsToLoad .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";
                            }
                            $newContent .= $this->createJsLink($content, $jsToLoad);
                        }
                    }
                }
            }else{
                /**
                 * do nothing
                 * just return the original content
                 */
                $newContent = $content;
            }
        }else{
            /**
             * do nothing
             * just return the original content
             */
            $newContent = $content;
        }
        return $newContent;
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