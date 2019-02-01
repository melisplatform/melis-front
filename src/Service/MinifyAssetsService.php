<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MelisCore\Service\MelisCoreGeneralService;
use MatthiasMullie\Minify;

class MinifyAssetsService extends MelisCoreGeneralService
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
        $errors = array();
        $siteName = '';
        try {
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites';

            if(!empty($arrayParameters['siteId'])) {
                //compile only this site
                $melisEngineTableSite = $this->getServiceLocator()->get('MelisEngineTableSite');
                //get the site information by site id
                $sites = $melisEngineTableSite->getSiteById($arrayParameters['siteId'], getenv('MELIS_PLATFORM'))->toArray();
                if(!empty($sites)) {
                    foreach ($sites as $key => $site) {
                        $siteName = $site['site_name'];
                        $siteConfigDir = $dir.'/'.$siteName.'/config/assets.config.php';
                        //check if assets config is exist
                        if(file_exists($siteConfigDir)){
                            //get the content of the asset config
                            $files = include($siteConfigDir);
                            //process the assets to make a bundle
                            $errors = $this->generateAllAssets($files, $dir, $site['site_name']);
                        }else{
                            $errors = array('Error' => $site['site_name'].'/config/assets.config.php not found.');
                        }
                    }
                }
            }else{
                //compile all assets in every site
            }
        }catch (\Exception $ex){
           $errors = array('Error' => $ex->getMessage());
        }

        $arrayParameters['result'] = array(
            'errors' => $errors,
            'siteName' => $siteName,
            'siteId' => $arrayParameters['siteId']
        );
        $this->sendEvent('melis_front_minify_assets_end', $arrayParameters);

        return $arrayParameters['result'];
    }

    /**
     * Function to generate all assets
     *
     * @param $files
     * @param $dir
     * @param $siteName
     * @return array
     */
    public function generateAllAssets ($files, $dir, $siteName)
    {
        $cssMinifier = new Minify\CSS();
        $jsMinifier = new Minify\JS();

        $bundleDir = $dir.'/'.$siteName.'/public/';

        $messages = array();
        if(!empty($files)){
            foreach($files as $key => $file){
                //create a bundle for js
                $key = strtolower($key);
                if($key == 'js'){
                    $messages['Js'] = $this->createBundleFile($jsMinifier,'bundle.js', $file, $dir, $bundleDir);
                }
                //create a bundle for css
                if($key == 'css'){
                    $messages['Css'] = $this->createBundleFile($cssMinifier, 'bundle.css', $file, $dir, $bundleDir);
                }
            }
        }
        return $messages;
    }

    /**
     * Function to create a bundle
     *
     * @param $minifier
     * @param $fileName
     * @param $files
     * @param $dir
     * @param $bundleDir
     * @return array
     */
    private function createBundleFile ($minifier, $fileName, $files, $dir, $bundleDir)
    {
        $translator = $this->getServiceLocator()->get('translator');
        $error = array();
        //check if the directory for the bundle is exist
        if($this->checkDir($bundleDir)) {
            //check if bundle is writable
            if (is_writable($bundleDir)) {
                if (!empty($files)) {
                    try {
                        foreach ($files as $key => $file) {
                            //add the file to minify later
                            $minifier->add($dir . $file);
//                            $minifier = $this->getFileContentsViaCurl($minifier, $file);
                        }
                        //minify all the files
                        $minifier->minify($bundleDir . $fileName);
                        array_push($error, $translator->translate('tr_front_minify_assets_compiled_successfully'));
                    } catch (\Exception $ex) {
                        /**
                         * this will occur only if the bundle.css or bundle.js
                         * is not writable or no permission
                         */
                        array_push($error, wordwrap($ex->getMessage(), 20, "\n", true));
                    }
                }else{
                    array_push($error, $translator->translate('tr_front_minify_assets_nothing_to_compile'));
                }
            } else {
                array_push($error, $bundleDir . ' is not writable.');
            }
        }else{
            array_push($error, $bundleDir . ' does not exist.');
        }
        return $error;
    }

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

    private function getRenAssets($assetsDir)
    {
        $modules = array();
        if($this->checkDir($assetsDir)) {
            $modules = $this->getDir($assetsDir);
        }

        return $modules;
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
}