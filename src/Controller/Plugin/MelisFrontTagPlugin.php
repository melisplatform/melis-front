<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use MelisFront\Navigation\MelisFrontNavigation;
use Zend\View\Model\ViewModel;

/**
 * This plugin implements the business logic of the
 * "Tag" plugin.
 * 
 * Please look inside app.plugins.php for possible awaited parameters
 * in front and back function calls.
 * 
 * front() and back() are the only functions to create / update.
 * front() generates the website view
 * back() generates the plugin view in template edition mode (TODO)
 * 
 * Configuration can be found in $pluginConfig / $pluginFrontConfig / $pluginBackConfig
 * Configuration is automatically merged with the parameters provided when calling the plugin.
 * Merge detects automatically from the route if rendering must be done for front or back.
 * 
 * How to call this plugin without parameters:
 * $plugin = $this->MelisFrontTagPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisTagPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/tag/tag'
 * );
 * $pluginView = $plugin->render($parameters);
 * 
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'tag_01');
 * 
 * How to display in your controller's view:
 * echo $this->tag_01;
 * 
 * 
 */
class MelisFrontTagPlugin extends MelisTemplatingPlugin
{
    public function __construct($updatesPluginConfig = array())
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'melisTag';
        parent::__construct($updatesPluginConfig);
    }
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        if ($this->renderMode == 'melis' && empty($this->pluginFrontConfig['value']))
            $this->pluginFrontConfig['value'] = $this->pluginFrontConfig['default'];
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $this->pluginFrontConfig['id'],
            'value'        => $this->pluginFrontConfig['value'],
            'widthDesktop' => $this->pluginFrontConfig['widthDesktop'],
            'widthTablet' => $this->pluginFrontConfig['widthTablet'],
            'widthMobile' => $this->pluginFrontConfig['widthMobile']
        );
        
        // return the variable array and let the view be created
        return $viewVariables;
    }
    
    // Redefining the back function as the display of tags is specific with TinyMce
    public function back()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('MelisFront/tag/meliscontainer');
        
        $viewModel->configPluginKey = $this->configPluginKey;
        $viewModel->pluginName = $this->pluginName;
        $viewModel->pluginBackConfig = $this->pluginBackConfig;
        $viewModel->pluginFrontConfig = $this->pluginFrontConfig;
        $viewModel->pluginHardcoded = $this->pluginHardcoded;
        $viewModel->hardcodedConfig = $this->updatesPluginConfig;
        $viewModel->fromDragDropZone = $this->fromDragDropZone;
        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : 0;
        $viewModel->pageId = $pageId;
        $viewModel->pluginXmlDbKey = $this->pluginXmlDbKey;
        
        $viewModel->tagId = $this->pluginFrontConfig['id'];
        $viewModel->type = $this->pluginFrontConfig['type'];
        $viewModel->widthDesktop = $this->pluginFrontConfig['widthDesktop'];
        $viewModel->widthTablet = $this->pluginFrontConfig['widthTablet'];
        $viewModel->widthMobile = $this->pluginFrontConfig['widthMobile'];
        $viewModel->configPluginKey = $this->configPluginKey;
        $viewModel->fromDragDropZone = $this->fromDragDropZone;
        
        $siteModule = getenv('MELIS_MODULE');
        $melisPage = $this->getServiceLocator()->get('MelisEnginePage');
        $datasPage = $melisPage->getDatasPage($pageId, 'saved');
        if($datasPage)
        {
            $datasTemplate = $datasPage->getMelisTemplate();
            
            if(!empty($datasTemplate))
            {
                $siteModule = $datasTemplate->tpl_zf2_website_folder;
            }
        }
        
        
        $viewModel->siteModule = $siteModule;
        
        return $viewModel;
    }
    
    public function loadDbXmlToPluginConfig()
    {
        $configValues = array();
        
        $xml = simplexml_load_string($this->pluginXmlDbValue);
        if ($xml)
        {
            if (!empty($xml->attributes()->type))
                $configValues['type'] = (string)$xml->attributes()->type;

            if (!empty($xml->attributes()->width_desktop))
                $configValues['widthDesktop'] = (string)$xml->attributes()->width_desktop;

            if (!empty($xml->attributes()->width_tablet))
                $configValues['widthTablet'] = (string)$xml->attributes()->width_tablet;

            if (!empty($xml->attributes()->width_mobile))
                $configValues['widthMobile'] = (string)$xml->attributes()->width_mobile;

            $configValues['value'] = (string)$xml;
        }
        
        return $configValues;
    }
    
    public function savePluginConfigToXml($parameters)
    {

        $xmlValueFormatted = '';
        if (!empty($parameters['tagValue']))
            $tagValue = $parameters['tagValue'];
        else
            $tagValue = '';

        $widthDesktop = isset($parameters['melisPluginDesktopWidth']) ? $parameters['melisPluginDesktopWidth'] : $this->widthDesktop;
        $widthTablet  = isset($parameters['melisPluginTabletWidth'])  ? $parameters['melisPluginTabletWidth']  : $this->widthTablet;
        $widthMobile  = isset($parameters['melisPluginMobileWidth'])  ? $parameters['melisPluginMobileWidth']  : $this->widthMobile;

        $type = null;
        if(isset($parameters['tagType'])) {
            $type = $parameters['tagType'];
        }
        else {
            if(isset($parameters['melisPluginName'])) {
                $pluginName = $parameters['melisPluginName'];
                $regex = '/MelisFrontTag([a-zA-Z]*)Plugin/';
                if(preg_match($regex, $pluginName, $matches)) {
                    $type = isset($matches[1]) ? strtolower($matches[1]) : 'html';
                }
            }
        }

        $id = null;
        if(isset($parameters['tagId'])) {
            $id = $parameters['tagId'];
        }
        else {
            if(isset($parameters['melisPluginId'])) {
                $id = $parameters['melisPluginId'];
            }
        }

        $xmlValueFormatted = "\t" . '<melisTag id="' . $id .
            '" type="' . $type . '" width_desktop="'.$widthDesktop.'" '.
            'width_tablet="'.$widthTablet.'" width_mobile="'.$widthMobile.'"><![CDATA[' .
            $tagValue . ']]></melisTag>' . "\n";

        return $xmlValueFormatted;
    }

    public function getResponsiveContent()
    {
        $newContent = '<div class="'.'plugin-width-lg-'.round($this->widthDesktop) .
            ' ' . 'plugin-width-lg-'.round($this->widthDesktop) .
            ' ' . 'plugin-width-lg-'.round($this->widthDesktop).'">'.
            $this->pluginFrontConfig['value'].'</div>';

        return $newContent;
    }
}
