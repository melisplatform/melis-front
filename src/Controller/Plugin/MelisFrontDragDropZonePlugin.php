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
        $html = '';

        // Looping on plugins found inside the dragdrop zone to render them through their plugins
        // and get the HTML resulting in everything

        $plugins     = array();
        $containerId = null;



        // dump($this->pluginFrontConfig['id']);
        // if (FALSE)
        foreach ($this->pluginFrontConfig['plugins'] as $plugin) {

            $tmpHtml = null;
            $datas = array(
                'action' => 'getPlugin',
                'module' => $plugin['pluginModule'],
                'pluginName' => $plugin['pluginName'],
                'pluginId' => $plugin['pluginId'],
                'pageId' => $this->pluginFrontConfig['pageId'],
                'fromDragDropZone' => true,
            );

            try {
                $forwardPlugin = $this->getController()->forward();

                $jsonResults = $forwardPlugin->dispatch('MelisFront\\Controller\\MelisPluginRenderer', $datas);


                if (!empty($jsonResults)) {
                    $variables = $jsonResults->getVariables();
                    $containerId = isset($variables['datas']['dom']['pluginContainerId']) ?
                        $variables['datas']['dom']['pluginContainerId'] : count($plugins);

                    if (!empty($variables['success'])) {
                        //$html .= $variables['datas']['html'];
                        $tmpHtml = $variables['datas']['html'];
                    } else {
                        // problem with the plugins, we show the error only BO side
                        if ($this->renderMode == 'melis') {
                            //$html .= $variables['errors'] . ' : ' . $plugin['pluginModule'] . ' / ' . $plugin['pluginName'];
                            $tmpHtml = $variables['errors'] . ' : ' . $plugin['pluginModule'] . ' / ' . $plugin['pluginName'];
                        }
                    }
                }
            } catch (\Exception $e) {
                return array('pluginId' => $this->pluginFrontConfig['id']);
            }

            $plugins[$containerId][] = $tmpHtml;
        }


        // add container to dragdropzone

        $newHtml = !$this->isInBackOffice() ? '<div class="clearfix">' : '';
        foreach ($plugins as $containerId => $contents) {
            foreach ($contents as $idx => $content) {
                $newHtml .= "\t" . $content;
            }
        }

        $newHtml .= !$this->isInBackOffice() ? '</div>' : '';

        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $this->pluginFrontConfig['id'],
            'pluginsHtml' => $newHtml,
        );

        // return the variable array and let the view be created
        return $viewVariables;
    }

    // Redefining the back function as the display of tags is specific with TinyMce
    public function back()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('MelisFront/dragdropzone/meliscontainer');
        $translator = $this->getServiceManager()->get('translator');

        // dump($this->pluginFrontConfig);
        // dd($this->pluginFrontConfig['id']);
        // dump($this->pluginFrontConfig['id']);
        $viewModel->pluginFrontConfig = $this->pluginFrontConfig;
        $viewModel->dragdropzoneId = $this->pluginFrontConfig['id'];
        $viewModel->configPluginKey = $this->configPluginKey;
        $viewModel->pluginName = $this->pluginName;
        $viewModel->pluginXmlDbKey = $this->pluginXmlDbKey;
        $viewModel->dragDropLabel = $translator->translate('tr_front_drag_drop_zone_label');

        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : 0;
        $columns = (!empty($this->pluginFrontConfig['columns'])) ? $this->pluginFrontConfig['columns'] : 1;

        $siteModule = getenv('MELIS_MODULE');
        $melisPage = $this->getServiceManager()->get('MelisEnginePage');
        $datasPage = $melisPage->getDatasPage($pageId, 'saved');
        if ($datasPage) {
            $datasTemplate = $datasPage->getMelisTemplate();

            if (!empty($datasTemplate)) {
                $siteModule = $datasTemplate->tpl_zf2_website_folder;
            }
        }

        $viewModel->hasDragDropZone = false;
        if ($this->pluginFrontConfig['plugins'])
            foreach ($this->pluginFrontConfig['plugins'] as $plugins)
                if ($plugins['pluginName'] == 'MelisFrontDragDropZonePlugin') {
                    $viewModel->hasDragDropZone = true;
                    break;
                }

        // dump($viewModel->hasDragDropZone);

        $viewModel->siteModule = $siteModule;
        $viewModel->columns = $columns;

        return $viewModel;
    }

    public function loadDbXmlToPluginConfig()
    {
        $configValues = array();

        $xml = simplexml_load_string($this->pluginXmlDbValue);

        if ($xml) {
            $cpt = 0;

            // dump('xml', $xml);

            foreach ($xml as $k => $plugin) {
                // dump($k);
                // dump($plugin->attributes()->id);
                // dump('test', $x->melisDragDropZone);
                // if (isset($x->melisDragDropZone))
                //     dump('melisDragDropZone', $x);
                // else
                //     dump('plugin', $x);

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


            // foreach ($xml->plugin as $key => $plugin) {
            //     $configValues[$cpt] = array();
            //     if (!empty($plugin->attributes()->module))
            //         $configValues[$cpt]['pluginModule'] = (string)$plugin->attributes()->module;
            //     if (!empty($plugin->attributes()->name))
            //         $configValues[$cpt]['pluginName'] = (string)$plugin->attributes()->name;
            //     if (!empty($plugin->attributes()->id))
            //         $configValues[$cpt]['pluginId'] = (string)$plugin->attributes()->id;

            //     $cpt++;
            // }
        }

        // dump($configValues);

        return array("plugins" => $configValues);
    }

    public function savePluginConfigToXml($parameters)
    {
        $xmlValueFormatted = '';
        //
        //        if (!empty($parameters['melisDragDropZoneListPlugin']) && count($parameters['melisDragDropZoneListPlugin']) > 0)
        //        {
        //            foreach ($parameters['melisDragDropZoneListPlugin'] as $plugin)
        //                $xmlValueFormatted .= "\t\t" . '<plugin module="' . $plugin['melisModule'] . '" name="' .
        //                    $plugin['melisPluginName'] . '" id="' . $plugin['melisPluginId'] . '"></plugin>' . "\n";
        //        }
        //


        if (!empty($parameters['children'])) {
            foreach ($parameters['children'] as $key => $val) {
                $xmlValueFormatted .= "\t\t" . '<' . $this->pluginXmlDbKey . ' id="' . $val['melisPluginId'] . '">';

                if (!empty($val['melisDragDropZoneListPlugin'])) {
                    foreach ($val['melisDragDropZoneListPlugin'] as $plugin) {
                        $xmlValueFormatted .= "\t\t" . '<plugin module="' . $plugin['melisModule'] . '" name="' .
                            $plugin['melisPluginName'] . '" id="' . $plugin['melisPluginId'] . '"></plugin>' . "\n";
                    }
                }
                $xmlValueFormatted .= '</' . $this->pluginXmlDbKey . '>' . "\n";
            }
        }

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
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

        return false;
    }
}
