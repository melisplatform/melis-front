<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use MelisFront\Navigation\MelisFrontNavigation;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;
/**
 * This plugin implements the business logic of the
 * "Breadcrumb" plugin.
 *
 * Please look inside app.plugins.php for possible awaited parameters
 * in front and back function calls.
 *
 * front() and back() are the only functions to create / update.
 * front() generates the website view
 *
 * Configuration can be found in $pluginConfig / $pluginFrontConfig / $pluginBackConfig
 * Configuration is automatically merged with the parameters provided when calling the plugin.
 * Merge detects automatically from the route if rendering must be done for front or back.
 *
 * How to call this plugin without parameters:
 * $plugin = $this->MelisFrontBreadcrumbPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontBreadcrumbPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/breadcrumb/breadcrumb'
 * );
 * $pluginView = $plugin->render($parameters);
 *
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'breadcrumb');
 *
 * How to display in your controller's view:
 * echo $this->breadcrumb;
 *
 *
 */
class MelisFrontBlockSectionPlugin extends MelisTemplatingPlugin
{

    public function __construct($updatesPluginConfig = array())
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'melisFrontBlockSectionPlugin';
        parent::__construct($updatesPluginConfig);
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {

        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $this->pluginXmlDbKey,
        );

        // return the variable array and let the view be created
        return $viewVariables;
    }

    public function back()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('MelisFront/block-section-container');
        $viewModel->configPluginKey    = $this->configPluginKey;
        $viewModel->pluginName         = $this->pluginName;
        $viewModel->pluginBackConfig   = $this->pluginBackConfig;
        $viewModel->pluginFrontConfig  = $this->pluginFrontConfig;
        $viewModel->pluginHardcoded    = $this->pluginHardcoded;
        $viewModel->hardcodedConfig    = $this->updatesPluginConfig;
        $viewModel->fromDragDropZone   = $this->fromDragDropZone;
        $viewModel->encapsulatedPlugin = $this->encapsulatedPlugin;

        $viewModel->widthDesktop      = $this->widthDesktop;
        $viewModel->widthTablet       = $this->widthTablet;
        $viewModel->widthMobile       = $this->widthMobile;
        $viewModel->pluginContainerId = $this->pluginContainerId;

        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : 0;
        $viewModel->pageId = $pageId;
        $viewModel->pluginXmlDbKey = $this->pluginXmlDbKey;

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


    /**
     * This method saves the XML version of this plugin in DB, for this pageId
     * Automatically called from savePageSession listenner in PageEdition
     */
    public function savePluginConfigToXml($parameters)
    {
        $xmlValueFormatted = '';

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }


}
