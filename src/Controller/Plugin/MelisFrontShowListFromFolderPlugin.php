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

/**
 * This plugin implements the business logic of the
 * "listofpages" plugin.
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
 * $plugin = $this->MelisFrontShowListFromFolderPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontShowListFromFolderPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/listofpages/show'
 * );
 * $pluginView = $plugin->render($parameters);
 * 
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'listofpages');
 * 
 * How to display in your controller's view:
 * echo $this->listofpages;
 */
class MelisFrontShowListFromFolderPlugin extends MelisTemplatingPlugin
{
    // the key of the configuration in the app.plugins.php
    public $configPluginKey = 'melisfront';
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        $listofpages = array();
        
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : null;
        
        // Getting the Subpages from MelisFrontNavigator
        $treeSrv = $this->getServiceLocator()->get('MelisEngineTree');
        $pages = $treeSrv->getPageChildren($pageId, 1);
        
        foreach ($pages As $key => $val)
        {
            /**
             * Page content has a values of XML type
             * so need to parse to make the content of the page easy to manage
             * 
             * $xmlValues will handle the result of parsing
             * this will return array
             */
            $parser = xml_parser_create();
            xml_parse_into_struct($parser, $val->page_content, $xmlValues);
            xml_parser_free($parser);
            
            // This process will get only the Page content with the tags of MELISTAGS and has a Tag Id
            $pageContent = array();
            foreach ($xmlValues As $xKey => $xVal)
            {
                if ($xVal['tag'] = 'MELISTAG')
                {
                    if (!empty($xVal['attributes']['ID']))
                    {
                        // Adding the content tag using the tag Id with its value
                        $pageContent[$xVal['attributes']['ID']] = $xVal['value'];
                    }
                }
            }
            
            $val->page_tags = $pageContent;
            
            array_push($listofpages, $val);
        }
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'listofpages' => $listofpages
        );
        
        // return the variable array and let the view be created
        return $viewVariables;
    }
    
    
    /**
     * This function return the back office rendering for the template edition system
     * TODO
     */
    public function back()
    {
        return array();
    }
}
