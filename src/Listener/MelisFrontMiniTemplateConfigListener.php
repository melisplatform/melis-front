<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * Prepare the mini template config
 * and put it inside the config service
 *
 * Class MelisFrontMiniTemplateConfigListener
 * @package MelisFront\Listener
 */
class MelisFrontMiniTemplateConfigListener
{
    private const MINI_TEMPLATES_FOLDER = 'miniTemplatesTinyMce';
    private const DS = DIRECTORY_SEPARATOR;

    public function onLoadModulesPost(ModuleEvent $e)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $e->getParam('ServiceManager');

        if(!empty($_SERVER['REQUEST_URI'])){
            $uri = $_SERVER['REQUEST_URI'];

            //we don't want listener to be executed if it's not a php code
            preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 1)
                return;

            $uri1 = '';
            $tabUri = explode('/', $uri);
            if (!empty($tabUri[1]))
                $uri1 = $tabUri[1];

            //check if we are in front
            if ($uri1 != 'melis')
            {
                //get the config listener
                $configListener = $e->getConfigListener();
                //get the merged config
                $config         = $configListener->getMergedConfig(false);

                $sitePath = array();
                /**
                 * get all the minitemplates from the site
                 * inside vendor
                 */
                $composerSrv = $serviceManager->get('MelisEngineComposer');
                $vendorModules = $composerSrv->getVendorModules();
                if (!empty($vendorModules)) {
                    foreach ($vendorModules as $key => $module) {
                        if ($composerSrv->isSiteModule($module)) {
                            $path = $composerSrv->getComposerModulePath($module);
                            if (!empty($path)) {
                                $path = $path . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'miniTemplatesTinyMce';
                                $sitePath[$module] = $path;
                            }
                        }
                    }
                }

                /**
                 * get all minitemplates from MelisSites
                 */
                $userSites = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites';
                if(file_exists($userSites) && is_dir($userSites)) {
                    $sites = $this->getDir($userSites);
                    if (!empty($sites)) {
                        foreach ($sites as $key => $val) {
                            //public site folder
                            $publicFolder = $userSites . DIRECTORY_SEPARATOR . $val . DIRECTORY_SEPARATOR . 'public';
                            //get the mini template folder path
                            $path = $publicFolder . DIRECTORY_SEPARATOR . 'miniTemplatesTinyMce';
                            $sitePath[$val] = $path;
                        }
                    }
                }

                //get the config for mini template
                if(!empty($this->prepareMiniTemplateConfig($sitePath))){
                    $config = ArrayUtils::merge($config, $this->prepareMiniTemplateConfig($sitePath));
                }

                // Pass the changed configuration back to the listener:
                $configListener->setMergedConfig($config);
                $e->setConfigListener($configListener);
                /**
                 * Update the config inside the service
                 */
                $serviceManager->setAllowOverride(true);
                $serviceManager->setService('Config', $config);
                $serviceManager->setAllowOverride(false);
            }
        }
    }

    /**
     * Function to prepare the Mini Template config
     *
     * @param $miniTplPath
     * @return array
     */
    public function prepareMiniTemplateConfig($miniTplPath)
    {
        $imgExtensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF'];
        $pluginsFormat = array();

        foreach ($miniTplPath as $siteName => $path) {

            if (file_exists($path) && is_dir($path)) {

                //get the plugin config format
                $pluginsConfig = include __DIR__ . '/../../config/plugins/MiniTemplatePlugin.config.php';
                if (!empty($pluginsConfig)) {

                    //get all the mini template
                    $tpls = array_map(function ($tpl) {
                        return basename($tpl);
                    }, glob("$path/*.{phtml,PHTML,php,PHP,html,HTML}", GLOB_BRACE));

                    // Get the list of images inside the mini templates folder
                    $availableThumbnails = array_map(function ($tpl) {
                        return basename($tpl);
                    }, glob("$path/*.{" . implode(',', $imgExtensions) . "}", GLOB_BRACE));

                    if (!empty($tpls)) {
                        //set the site name as sub category title
                        $pluginsConfig['melis']['subcategory']['title'] = $siteName;
                        //set the id of the plugin
                        $pluginsConfig['melis']['subcategory']['id'] = $pluginsConfig['melis']['subcategory']['id'] . '_' . $siteName;
                        //get the content of the mini template
                        foreach ($tpls as $k => $v) {
                            //remove the file extension from the filename
                            $name = pathinfo($v, PATHINFO_FILENAME);
                            //create a plugin post name
                            $postName = $k . strtolower($name) . '_' . strtolower($siteName);
                            //prepare the content of the mini template
                            $content = $path . DIRECTORY_SEPARATOR . $v;
                            //set the default layout for the plugin based on mini template
                            $pluginsConfig['front']['default'] = file_get_contents($content);
                            //set the plugin name using the template name
                            $pluginsConfig['melis']['name'] = $name;

                            /**
                             * Set plugin thumbmail
                             * - check MiniTemplate folder for a possible image file with the same name as the plugin
                             * - Set as thumbnail if image file is any of the available thumbnails
                             * - Otherwise, leave it blank (it will be up to the view file to set a default image)
                             */
                            $img = null;
                            foreach ($imgExtensions as $ext) {
                                if (in_array("$name.$ext", $availableThumbnails)) {
                                    $img = self::DS . $siteName . self::DS . self::MINI_TEMPLATES_FOLDER . self::DS;
                                    $img .= "$name.$ext";
                                    break;
                                }
                                // thumbnail filename in UPPER CASE
                                if (in_array(strtoupper($name) . ".$ext", $availableThumbnails)) {
                                    $img = self::DS . $siteName . self::DS . self::MINI_TEMPLATES_FOLDER . self::DS;
                                    $img .= strtoupper($name) . ".$ext";
                                    break;
                                }
                            }
                            $pluginsConfig['melis']['thumbnail'] = $img;

                            //include the mini tpl plugin config
                            $pluginsFormat['plugins']['MelisMiniTemplate']['plugins']['MiniTemplatePlugin_' . $postName] = $pluginsConfig;
                        }
                    }
                }
            }
        }

        return $pluginsFormat;
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