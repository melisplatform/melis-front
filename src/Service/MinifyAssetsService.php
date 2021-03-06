<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MatthiasMullie\Minify;
use MelisCore\Service\MelisGeneralService;

class MinifyAssetsService extends MelisGeneralService
{
    /**
     * @param null $siteId
     * @return array
     */
    public function minifyAssets ($siteId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_front_minify_assets_start', $arrayParameters);
        $results = array();
        try {
            if(!empty($arrayParameters['siteId'])) {
                /**
                 * This will compile only the assets
                 * of the specified site
                 */
                $melisEngineTableSite = $this->getServiceManager()->get('MelisEngineTableSite');
                //get the site information by site id
                $sites = $melisEngineTableSite->getSiteById($arrayParameters['siteId'], getenv('MELIS_PLATFORM'))->toArray();
                if(!empty($sites)) {
                    foreach ($sites as $key => $site) {
                        $siteName = $site['site_name'];
                        /**
                         * This will check if the sites is came from
                         * the vendor or it came from the MelisSites
                         */
                        if(!empty($this->getSitesVendorPath($siteName))) {
                            $sitePath = $this->getSitesVendorPath($siteName);
                            $siteConfigDir = $sitePath.'/config/assets.config.php';
                            $fromVendor = true;
                        }else{
                            $sitePath = $this->getSitesModulePath();
                            $siteConfigDir = $sitePath.'/'.$siteName.'/config/assets.config.php';
                            $fromVendor = false;
                        }
                        //check if assets config is exist
                        if(file_exists($siteConfigDir)){
                            //get the content of the asset config
                            $files = include($siteConfigDir);
                            //process the assets to make a bundle
                            $results = $this->generateAllAssets($files, $sitePath, $site['site_name'], $fromVendor);
                            $this->saveBundleDateTimeToDb($arrayParameters['siteId']);
                        }else{
                            $results = array('error' => array('message' => $siteConfigDir.' not found.', 'success' => false));
                        }
                    }
                }
            }else{
                /**
                 * This will compile all the assets
                 * in every sites from MelisSites
                 */
                $sitePath = $this->getSitesModulePath();
                $sitesList = $this->getAllSites($sitePath);
                foreach($sitesList as $key => $site){
                    $siteConfigDir = $sitePath.'/'.$site.'/config/assets.config.php';
                    if(file_exists($siteConfigDir)){
                        //get the content of the asset config
                        $files = include($siteConfigDir);
                        //process the assets to make a bundle
                        $res = $this->generateAllAssets($files, $sitePath, $site, false);
                        array_push($results, array($site => $res));
                    }else{
                        array_push($results, array('error' => array('message' => $siteConfigDir.' not found.', 'success' => false)));
                    }
                }

                /**
                 * This will minify all the SITE assets in
                 * vendor
                 */
                if($results['error']['success']){
                    $siteVendorList = $this->minifySitesVendorAssets();
                    foreach($siteVendorList as $sitePath){
                        $siteConfigDir = $sitePath['path'].'/config/assets.config.php';
                        if(file_exists($siteConfigDir)){
                            //get the content of the asset config
                            $files = include($siteConfigDir);
                            //process the assets to make a bundle
                            $res = $this->generateAllAssets($files, $sitePath['path'], $sitePath['module'], true);
                            array_push($results, array($sitePath['module'] => $res));
                        }else{
                            array_push($results, array('error' => array('message' => $siteConfigDir.' not found.', 'success' => false)));
                        }
                    }
                }
            }
        }catch (\Exception $ex){
            $results = array('error' => array('message' => $ex->getMessage(), 'success' => false));
        }

        $arrayParameters['result'] = array(
            'results' => $results,
            'siteId' => $arrayParameters['siteId']
        );
        $this->sendEvent('melis_front_minify_assets_end', $arrayParameters);

        return $arrayParameters['result'];
    }

    /**
     * @return array
     */
    public function minifySitesVendorAssets()
    {
        $moduleSrv = $this->getServiceManager()->get('MelisAssetManagerModulesService');
        $vendordModules = $moduleSrv->getVendorModules();

        $moduleFolders = array();
        foreach ($vendordModules as $key => $module){
            //check if module is site
            if($moduleSrv->isSiteModule($module)){
                //get the full path of the site module
                $path = $moduleSrv->getComposerModulePath($module);
                array_push($moduleFolders, array('path' => $path, 'module' => $module));
            }
        }
        return $moduleFolders;
    }

    /**
     * Function to generate all assets
     *
     * @param $files
     * @param $sitePath
     * @param $siteName
     * @param $isFromVendor
     * @return array
     */
    public function generateAllAssets ($files, $sitePath, $siteName, $isFromVendor)
    {
        $cssMinifier = new Minify\CSS();
        $jsMinifier = new Minify\JS();

        if($isFromVendor){
            $bundleDir = $sitePath.'/public/';
            /**
             * we need to remove the site name on the path
             * since the site name is already included
             * in the file name inside the assets.config
             */
            $sitePath = dirname($sitePath);
        }else{
            $bundleDir = $sitePath.'/'.$siteName.'/public/';
        }

        $messages = array();

        //check if the directory for the bundle is exist
        if($this->checkDir($bundleDir)) {
            //check if bundle is writable
            if (is_writable($bundleDir)) {

                if (!empty($files)) {
                    foreach ($files as $key => $file) {
                        //create a bundle for js
                        $key = strtolower($key);
                        if ($key == 'js') {
                            $messages['error'] = $this->createBundleFile($jsMinifier, 'bundle.js', $file, $sitePath, $bundleDir);
                        }
                        //create a bundle for css
                        if ($key == 'css') {
                            $messages['error'] = $this->createBundleFile($cssMinifier, 'bundle.css', $file, $sitePath, $bundleDir, false);
                        }
                    }
                }
            }else {
                $messages['error'] = array('message' => $bundleDir . ' is not writable.', 'success' => false);
            }
        }else{
            $messages['error'] = array('message' => $bundleDir . ' does not exist.', 'success' => false);
        }
        return $messages;
    }

    /**
     * Function to create a bundle
     *
     * @param $minifier
     * @param $fileName
     * @param $files
     * @param $sitePath
     * @param $bundleDir
     * @param $cleanCode
     * @return array
     */
    private function createBundleFile ($minifier, $fileName, $files, $sitePath, $bundleDir, $cleanCode = true)
    {
        $translator = $this->getServiceManager()->get('translator');
        $message = '';
        $success = false;
        if (!empty($files)) {
            try {
                foreach ($files as $key => $file) {
                    //remove comments
                    if($cleanCode) {
                        $codeToAdd = $this->removeComments($sitePath . $file);
                    }else{
                        $codeToAdd = $sitePath . $file;
                    }
                    //add the file to minify later
                    $minifier->add($codeToAdd);
//                 $minifier = $this->getFileContentsViaCurl($minifier, $file);
                }
                //minify all the files
                $minifier->minify($bundleDir . $fileName);
                $message = $translator->translate('tr_front_minify_assets_compiled_successfully');
                $success = true;
            } catch (\Exception $ex) {
                /**
                 * this will occur only if the bundle.css or bundle.js
                 * is not writable or no permission or other errors
                 */
                $message = wordwrap($ex->getMessage(), 20, "\n", true);
            }
        }else{
            $success = true;
        }

        return array(
            'message' => $message,
            'success' => $success
        );
    }

    /**
     * @param $minifier
     * @param $url
     * @return mixed
     */
    public function getFileContentsViaCurl($minifier, $url)
    {
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $_SERVER['HTTP_HOST'].$url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $minifier->add(curl_exec($curlSession));
        curl_close($curlSession);
        return $minifier;
    }

    /**
     * @param $sitesDir
     * @return array
     */
    private function getAllSites($sitesDir)
    {
        $modules = array();
        if($this->checkDir($sitesDir)) {
            $modules = $this->getDir($sitesDir);
        }

        return $modules;
    }

    /**
     * @return string
     */
    private function getSitesModulePath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites';
    }

    /**
     * @param $siteName
     * @return mixed
     */
    private function getSitesVendorPath($siteName)
    {
        $moduleSrv = $this->getServiceManager()->get('MelisAssetManagerModulesService');
        return $moduleSrv->getComposerModulePath($siteName);
    }

    /**
     * Remove comments from the file
     * to make it will be minified
     *
     * @param $fileStr
     * @return string|string[]|null
     */
    private function removeComments($fileStr)
    {
        $fileStr = file_get_contents($fileStr);
        $text = preg_replace('!/\*.*?\*/!s', '', $fileStr);
        return $text;
    }

    /**
     * This will check if directory exists and it's a valid directory
     * @param $dir
     * @return bool
     */
    protected function checkDir($dir)
    {
        if(file_exists($dir) && is_dir($dir))
        {
            return true;
        }

        return false;
    }

    /**
     * Returns all the sub-folders in the provided path
     * @param String $dir
     * @param array $excludeSubFolders
     * @return array
     */
    protected function getDir($dir, $excludeSubFolders = array())
    {
        $directories = array();
        if(file_exists($dir)) {
            $excludeDir = array_merge(array('.', '..', '.gitignore'), $excludeSubFolders);
            $directory  = array_diff(scandir($dir), $excludeDir);

            foreach($directory as $d) {
                if(is_dir($dir.'/'.$d)) {
                    $directories[] = $d;
                }
            }

        }

        return $directories;
    }

    public function saveBundleDateTimeToDb($siteId)
    {
        $bundleId = null;
        $table = $this->getServiceManager()->get('MelisEngineTableCmsSiteBundle');
        $bundleData = $this->getSiteBundleData($siteId);

        if (! empty($bundleData)) {
            $bundleId = $bundleData->bun_id;
        }

        $table->save([
            'bun_site_id' => $siteId,
            'bun_version_datetime' => date('YmdHis')
        ], $bundleId);
    }
    public function getSiteBundleData($siteId)
    {
        $table = $this->getServiceManager()->get('MelisEngineTableCmsSiteBundle');
        return $table->getEntryByField('bun_site_id', $siteId)->current();
    }
}