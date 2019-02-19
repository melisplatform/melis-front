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
        $results = array();
        try {
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites';

            if(!empty($arrayParameters['siteId'])) {
                /**
                 * This will compile only the assets
                 * of the specified site
                 */
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
                            $res = $this->generateAllAssets($files, $dir, $site['site_name']);
                            array_push($results, array($siteName => $res));

                        }else{
                            array_push($results, array($siteName => $site.'/config/assets.config.php not found.'));
                        }
                    }
                }
            }else{
                /**
                 * This will compile all the assets
                 * in every sites
                 */
                $sitesList = $this->getAllSites($dir);
                foreach($sitesList as $key => $site){
                    $siteConfigDir = $dir.'/'.$site.'/config/assets.config.php';
                    if(file_exists($siteConfigDir)){
                        //get the content of the asset config
                        $files = include($siteConfigDir);
                        //process the assets to make a bundle
                        $res = $this->generateAllAssets($files, $dir, $site);
                        array_push($results, array($site => $res));
                    }else{
                        array_push($results, array($site => $site.'/config/assets.config.php not found.'));
                    }
                }
            }
        }catch (\Exception $ex){
           $results = array('Error' => $ex->getMessage());
        }

        $arrayParameters['result'] = array(
            'results' => $results,
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

        //check if the directory for the bundle is exist
        if($this->checkDir($bundleDir)) {
            //check if bundle is writable
            if (is_writable($bundleDir)) {

                if (!empty($files)) {
                    foreach ($files as $key => $file) {
                        //create a bundle for js
                        $key = strtolower($key);
                        if ($key == 'js') {
                            $messages['error'] = $this->createBundleFile($jsMinifier, 'bundle.js', $file, $dir, $bundleDir);
                        }
                        //create a bundle for css
                        if ($key == 'css') {
                            $messages['error'] = $this->createBundleFile($cssMinifier, 'bundle.css', $file, $dir, $bundleDir);
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
     * @param $dir
     * @param $bundleDir
     * @return array
     */
    private function createBundleFile ($minifier, $fileName, $files, $dir, $bundleDir)
    {
        $translator = $this->getServiceLocator()->get('translator');
        $result = array();
        $message = '';
        $success = false;
        if (!empty($files)) {
            try {
                foreach ($files as $key => $file) {
                    //add the file to minify later
                    $minifier->add($dir . $file);
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
//          $message = $translator->translate('tr_front_minify_assets_nothing_to_compile');
            $success = true;
        }

        return array(
            'message' => $message,
            'success' => $success
        );
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

    private function getAllSites($sitesDir)
    {
        $modules = array();
        if($this->checkDir($sitesDir)) {
            $modules = $this->getDir($sitesDir);
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