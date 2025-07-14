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
class MelisFrontDragDropZonePlugin extends MelisTemplatingPlugin
{
    public function __construct($updatesPluginConfig = array())
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'melisDragDropZone';
        parent::__construct($updatesPluginConfig);
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Looping on plugins found inside the dragdrop zone to render them through their plugins
        // and get the HTML resulting in everything

        $plugins = [];

        $dndCtr = 1;

        if (isset($_GET['dndTpl']) && isset($_GET['dndId'])) {

            if (($_GET['dndId'] == $this->pluginFrontConfig['id']) || isset($_GET['addAction'])) {
                $config = $this->getServiceManager()->get('config');
                $dndLayouts = $config['plugins']['drag-and-drop-layouts'];

                $this->pluginFrontConfig['template_path'] = $dndLayouts[$_GET['dndTpl']]['template'];
            }
        }

        if ($this->pluginFrontConfig['isInnerDragDropZone'])
            foreach ($this->pluginFrontConfig['plugins'] as $plugin) {

                $pluginId = $plugin['pluginId'];
                if ($plugin['pluginName'] == 'MelisFrontDragDropZonePlugin') {
                    continue;
                }

                $tmpHtml = null;
                $data = array(
                    'action' => 'getPlugin',
                    'module' => $plugin['pluginModule'],
                    'pluginName' => $plugin['pluginName'],
                    'pluginId' => $pluginId,
                    'pageId' => $this->pluginFrontConfig['pageId'],
                    'fromDragDropZone' => true,
                );

                try {
                    $forwardPlugin = $this->getController()->forward();

                    $jsonResults = $forwardPlugin->dispatch('MelisFront\\Controller\\MelisPluginRenderer', $data);


                    if (!empty($jsonResults)) {
                        $variables = $jsonResults->getVariables();
                        // $containerId = isset($variables['datas']['dom']['pluginContainerId']) ?
                        //     $variables['datas']['dom']['pluginContainerId'] : count($plugins);

                        if (!empty($variables['success'])) {
                            $tmpHtml = $variables['datas']['html'];
                        } else {
                            // problem with the plugins, we show the error only BO side
                            if ($this->renderMode == 'melis') {
                                $tmpHtml = $variables['errors'] . ' : ' . $plugin['pluginModule'] . ' / ' . $plugin['pluginName'];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return array('pluginId' => $this->pluginFrontConfig['id']);
                }

                $plugins[$dndCtr++][] = $tmpHtml;
            }

        $newHtml = !$this->isInBackOffice() ? '<div class="clearfix">' : '';
        foreach ($plugins as $id => $contents) {
            foreach ($contents as $content) {
                $newHtml .= "\t" . $content;
            }
        }

        // apply content with layout to html result
        $newHtml .= !$this->isInBackOffice() ? '</div>' : '';

        //get dnd render mode
        $engineSiteSerice = $this->getServiceManager()->get('MelisEngineSiteService');
        $dndRenderMode = $engineSiteSerice->getSiteDNDRenderModeByPageId($this->pluginFrontConfig['pageId']);

        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $this->pluginFrontConfig['id'],
            'pluginsHtml' => $newHtml,
            'dndRenderMode' => $dndRenderMode
        );

        // return the variable array and let the view be created
        return $viewVariables;
    }

    // Redefining the back function as the display of tags is specific with TinyMce
    public function back()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('MelisFront/dragdropzone/meliscontainer-old');
        $translator = $this->getServiceManager()->get('translator');

        $viewModel->pluginFrontConfig = $this->pluginFrontConfig;
        $viewModel->dragdropzoneId = $this->pluginFrontConfig['id'];
        $viewModel->configPluginKey = $this->configPluginKey;
        $viewModel->pluginName = $this->pluginName;
        $viewModel->pluginXmlDbKey = $this->pluginXmlDbKey;
        $viewModel->dragDropLabel = $translator->translate('tr_front_drag_drop_zone_label');

        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : 0;
        $columns = (!empty($this->pluginFrontConfig['columns'])) ? $this->pluginFrontConfig['columns'] : 1;

        $engineSiteSerice = $this->getServiceManager()->get('MelisEngineSiteService');
        $dndRenderMode = $engineSiteSerice->getSiteDNDRenderModeByPageId($pageId);
        //if render dnd mode is empty, we won't use the dynamic dnd, we use the old style
        if($dndRenderMode == 'bootstrap')
            $viewModel->setTemplate('MelisFront/dragdropzone/meliscontainer');

        $siteModule = getenv('MELIS_MODULE');
        $melisPage = $this->getServiceManager()->get('MelisEnginePage');
        $datasPage = $melisPage->getDatasPage($pageId, 'saved');
        if ($datasPage) {
            $datasTemplate = $datasPage->getMelisTemplate();

            if (!empty($datasTemplate)) {
                $siteModule = $datasTemplate->tpl_zf2_website_folder;
            }
        }

        $config = $this->getServiceManager()->get('config');
        $dndLayouts = $config['plugins']['drag-and-drop-layouts'];
        $viewModel->dndLayouts =  $dndLayouts;
        $viewModel->pageId = $pageId;

        $viewModel->hasDragDropZone = false;
        if ($this->pluginFrontConfig['template_path'] != 'MelisFront/dnd-default-tpl')
            $viewModel->hasDragDropZone = true;

        $viewModel->siteModule = $siteModule;
        $viewModel->columns = $columns;
        $viewModel->dndLayoutTemplate = $this->pluginFrontConfig['template_path'];
        $viewModel->dndPluginReferer = $this->pluginFrontConfig['plugin_referer'];

        return $viewModel;
    }

    public function loadDbXmlToPluginConfig()
    {
        $configValues = array();
        if (!$this->pluginConfig['front']['isInnerDragDropZone'])
            return $configValues;

        $xml = simplexml_load_string($this->pluginXmlDbValue);

        // default dnd template
        $template = 'MelisFront/dnd-default-tpl';

        // default no plugin referer/ no origin
        $pluginReferer = '';

        if ($xml) {
            $cpt = 0;

            // plugin layout
            if (!empty($xml->attributes()->template))
                $template = (string)$xml->attributes()->template;

            if (!empty($xml->attributes()->plugin_referer))
                $pluginReferer = (string)$xml->attributes()->plugin_referer;

            foreach ($xml as $k => $plugin) {

                // skipping layout attr from plugins config
                if ($k == 'layout')
                    continue;

                $configValues[$cpt] = [];

                if ($k == 'melisDragDropZone') {
                    $configValues[$cpt]['pluginModule'] = 'melisfront';
                    $configValues[$cpt]['pluginName'] = 'MelisFrontDragDropZonePlugin';
                    $configValues[$cpt]['pluginId'] = (string)$plugin->attributes()->id;
                } else {

                    if (!empty($plugin->attributes()->module))
                        $configValues[$cpt]['pluginModule'] = (string)$plugin->attributes()->module;
                    if (!empty($plugin->attributes()->name))
                        $configValues[$cpt]['pluginName'] = (string)$plugin->attributes()->name;
                    if (!empty($plugin->attributes()->id))
                        $configValues[$cpt]['pluginId'] = (string)$plugin->attributes()->id;
                }

                $cpt++;
            }
        }

        return [
            'template_path' => $template,
            'plugin_referer' => $pluginReferer,
            "plugins" => $configValues,
            "pluginXmlDbValue"  => $this->pluginXmlDbValue

        ];
    }

    /**
     * @param $parameters
     * @return string
     */
    public function savePluginConfigToXml($parameters)
    {
        $xml = $this->buildXmlFromArray($parameters);
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $xmlValueFormatted = $dom->saveXML($dom->documentElement);
        return $xmlValueFormatted;
    }

    /**
     * @param $data
     * @param null $xml
     * @return null|\SimpleXMLElement
     */
    function buildXmlFromArray($data, $parent = null)
    {
        if (!$parent) {
            $parent = new \SimpleXMLElement('<' . $this->pluginXmlDbKey . '/>');
            $parent->addAttribute('id', $data['melisPluginId']);
            $parent->addAttribute('plugin_referer', $data['pluginReferer'] ?? '');
            $parent->addAttribute('plugin_position', '1');
            //            $parent->addAttribute('plugin_container_id', '');
            //            $parent->addAttribute('width_desktop', '100');
            //            $parent->addAttribute('width_tablet', '100');
            //            $parent->addAttribute('width_mobile', '100');
            $parent->addAttribute('template', $data['dndLayout']);

            //            $layout = $parent->addChild("layout");
            //            $dom = dom_import_simplexml($layout);
            //            $domOwner = $dom->ownerDocument;
            //            $dom->appendChild($domOwner->createCDATASection($data['dndLayout']));

            if (isset($data['melisDragDropZoneListPlugin']) && is_array($data['melisDragDropZoneListPlugin'])) {
                foreach ($data['melisDragDropZoneListPlugin'] as $plugin) {
                    $pluginNode = $parent->addChild('plugin');
                    $pluginNode->addAttribute('module', $plugin['melisModule']);
                    $pluginNode->addAttribute('name', $plugin['melisPluginName']);
                    $pluginNode->addAttribute('id', $plugin['melisPluginId']);
                }
            }
        }

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $child) {
                $childNode = $parent->addChild($this->pluginXmlDbKey);
                $childNode->addAttribute('id', $child['melisPluginId']);
                $childNode->addAttribute('plugin_container_id', '');
                $childNode->addAttribute('plugin_referer', $child['pluginReferer'] ?? '');
                $childNode->addAttribute('plugin_position', '1');
                $childNode->addAttribute('width_desktop', '100');
                $childNode->addAttribute('width_tablet', '100');
                $childNode->addAttribute('width_mobile', '100');
                $childNode->addAttribute('template', $child['dndLayout'] ?? '');

                // Handle plugins inside this zone
                if (isset($child['melisDragDropZoneListPlugin']) && is_array($child['melisDragDropZoneListPlugin'])) {
                    foreach ($child['melisDragDropZoneListPlugin'] as $plugin) {
                        $pluginNode = $childNode->addChild('plugin');
                        $pluginNode->addAttribute('module', $plugin['melisModule']);
                        $pluginNode->addAttribute('name', $plugin['melisPluginName']);
                        $pluginNode->addAttribute('id', $plugin['melisPluginId']);
                    }
                }

                // Recursive call for nested children
                $this->buildXmlFromArray($child, $childNode);
            }
        }

        return $parent;
    }

    private function isInBackOffice()
    {
        $request    = $this->getController()->getRequest();
        $routeMatch = $this->getServiceManager()->get('router')->match($request);
        $routeName  = $routeMatch->getMatchedRouteName();
        $module     = explode('/', $routeName);

        if (isset($module[0]) && ($module[0] == 'melis-front')) {
            return true;
        }

        if (isset($module[1]) && ($module[1] == 'melis_front_melisrender')) {
            return true;
        }

        if (isset($module[1]) && ($module[1] == 'dnd-layout')) {
            return true;
        }

        return false;
    }
}
