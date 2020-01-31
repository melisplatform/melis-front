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
        $image_ext = ['PNG', 'png', 'JPG', 'jpg', 'JPEG', 'jpeg'];
        $pluginsFormat = array();
        foreach($miniTplPath as $siteName => $path) {
            if (file_exists($path) && is_dir($path)) {
                $tplImgList = [];
                //get the plugin config format
                $pluginsConfig = include __DIR__ . '/../../config/plugins/MiniTemplatePlugin.config.php';
                if (!empty($pluginsConfig)) {
                    //get all the mini template
                    $tpls = array_diff(scandir($path), array('..', '.'));
                    /**
                     * Remove all the images
                     */
                    foreach ($tpls as $key => $tpl){
                        foreach($image_ext as $ext){
                            //if image found, store the image path with the template name as the key
                            if(strpos($tpl, $ext) !== false) {
                                $fName = pathinfo($tpl, PATHINFO_FILENAME);
                                $tplImgList[$fName] = '/'.$siteName.'/miniTemplatesTinyMce/'.$fName.'.'.$ext;
                                //remove the image
                                unset($tpls[$key]);
                            }
                        }
                    }

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
                            $postName = $k . strtolower($name).'_'. strtolower($siteName);
                            //prepare the content of the mini template
                            $content = $path . DIRECTORY_SEPARATOR . $v;
                            //set the default layout for the plugin based on mini template
                            $pluginsConfig['front']['default'] = file_get_contents($content);
                            //set the plugin name using the template name
                            $pluginsConfig['melis']['name'] = $name;
                            //apply minitemplate thumbnail
                            $pluginsConfig['melis']['thumbnail'] = $tplImgList[$name] ?? '/MelisFront/plugins/images/default.jpg';
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