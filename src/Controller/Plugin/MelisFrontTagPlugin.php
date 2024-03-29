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
use Laminas\View\Model\ViewModel;
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

        $this->getWidths();

        $this->widthDesktop = $this->pluginFrontConfig['widthDesktop'];
        $this->widthTablet = $this->pluginFrontConfig['widthTablet'];
        $this->widthMobile = $this->pluginFrontConfig['widthMobile'];
        $this->pluginContainerId = $this->pluginFrontConfig['pluginContainerId'];

        $viewVariables = array(
            'pluginId' => $this->pluginFrontConfig['id'],
            'value' => $this->pluginFrontConfig['value'],
            'widthDesktop' => $this->convertToCssClass('desktop', $this->widthDesktop),
            'widthTablet'  => $this->convertToCssClass('tablet',$this->widthTablet),
            'widthMobile'  => $this->convertToCssClass('mobile',$this->widthMobile),
            'pluginContainerId' => $this->pluginContainerId
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
        $viewModel->configPluginKey = $this->configPluginKey;
        $viewModel->fromDragDropZone = $this->fromDragDropZone;

        $viewModel->widthDesktop      = $this->pluginFrontConfig['widthDesktop'];
        $viewModel->widthTablet       = $this->pluginFrontConfig['widthTablet'];
        $viewModel->widthMobile       = $this->pluginFrontConfig['widthMobile'];
        $viewModel->pluginContainerId = $this->pluginFrontConfig['pluginContainerId'];


        $siteModule = getenv('MELIS_MODULE');
        $melisPage = $this->getServiceManager()->get('MelisEnginePage');
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
            $type = isset($this->pluginFrontConfig['type']) ? $this->pluginFrontConfig['type'] : 'html';

            if (!empty($xml->attributes()->type) &&
                ($type == (string)$xml->attributes()->type))
                $configValues['type'] = (string)$xml->attributes()->type;

            if(!empty($xml->attributes()->width_desktop))
                $this->widthDesktop = (array) $xml->attributes()->width_desktop[0];

            if(!empty($xml->attributes()->width_tablet))
                $this->widthTablet = (array) $xml->attributes()->width_tablet[0];

            if(!empty($xml->attributes()->width_mobile))
                $this->widthMobile = (array) $xml->attributes()->width_mobile[0];

            if(!empty($xml->attributes()->plugin_container_id))
                $this->pluginContainerId = (array) $xml->attributes()->plugin_container_id[0];

            $configValues['value'] = (string)$xml;
        }

        return $configValues;
    }

    public function savePluginConfigToXml($parameters)
    {
        $xmlValueFormatted = '';
        $tagValue          = isset($parameters['tagValue']) ? $parameters['tagValue'] : null;
        $tagId             = isset($parameters['tagId'])    ? $parameters['tagId']    : null;
        $tagType           = isset($parameters['tagType'])  ? $parameters['tagType']    : null;
        if (!empty($tagValue))
            $tagValue = $tagValue;
        else
            $tagValue = '';

        $xmlValueFormatted = "\t" . '<melisTag id="' . $tagId .
            '" type="' . $tagType . '"><![CDATA[' .
            $tagValue . ']]></melisTag>' . "\n";
        return $xmlValueFormatted;
    }

    private function getWidths()
    {       
        // Create an array with the variables that will be available in the view
        $this->pluginFrontConfig['widthDesktop'] = (is_array($this->widthDesktop) && isset($this->widthDesktop[0])) ? $this->widthDesktop[0] : 100;
        $this->pluginFrontConfig['widthTablet']  = (is_array($this->widthTablet) && isset($this->widthTablet[0]))  ? $this->widthTablet[0]  : 100;
        $this->pluginFrontConfig['widthMobile']  = (is_array($this->widthMobile) && isset($this->widthMobile[0]))  ? $this->widthMobile[0]  : 100;
        $this->pluginFrontConfig['pluginContainerId']  = isset($this->pluginContainerId[0])  ? $this->pluginContainerId[0] : null;
    }

}