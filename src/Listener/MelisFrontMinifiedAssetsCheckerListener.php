<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\Http\Segment;

/**
 * Minified Assets Checker listener
 */
class MelisFrontMinifiedAssetsCheckerListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_FINISH,
            function(MvcEvent $e){

                // Get route match to know if we are displaying in back or front
                $routeMatch = $e->getRouteMatch();
                $sm = $e->getApplication()->getServiceManager();

                if($routeMatch) {

                    $params = $routeMatch->getParams();

                    if (!empty($params['module']))
                    {
                        $response = $e->getResponse();
                        $content = $response->getContent();
                        $newContent = $content;

                        $cssBundleLoaded = false;
                        $jsBundleLoaded = false;

                        $moduleSrv = $sm->get('MelisAssetManagerModulesService');
                        /**
                         * Check to determine where does the site came from.
                         * From vendor or MelisSites
                         */
                        $siteVendorPath = $moduleSrv->getComposerModulePath($params['module']);
                        if(!empty($siteVendorPath)){
                            $siteDir = $siteVendorPath.'/';
                            $isFromVendor = true;
                        }else{
                            $siteDir = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/'.$params['module'].'/';
                            $isFromVendor = false;
                        }

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
                            $newContent = $this->loadAssetsFromConfig($newContent, $configDir, 'js', $isFromVendor, $params['module']);
                        }elseif(!$cssBundleLoaded && $jsBundleLoaded){
                            $newContent = $this->loadAssetsFromConfig($newContent, $configDir, 'css', $isFromVendor, $params['module']);
                        }elseif(!$cssBundleLoaded && !$jsBundleLoaded){
                            $newContent = $this->loadAssetsFromConfig($newContent, $configDir, null, $isFromVendor, $params['module']);
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