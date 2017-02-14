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
 * "Breadcrumb" plugin.
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
class MelisFrontBreadcrumbPlugin extends MelisTemplatingPlugin
{
    // the key of the configuration in the app.plugins.php
    public $configPluginKey = 'melisfront';
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        $breadcrumb = array();
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        // Retrieving the pageId from config
        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : null;
        
        $treeSrv = $this->getServiceLocator()->get('MelisEngineTree');
        $pageBreadcrumb = $treeSrv->getPageBreadcrumb($pageId, 0, true);
        
        if (is_array($pageBreadcrumb))
        {
            foreach ($pageBreadcrumb As $key => $val)
            {
                if (in_array($val->page_type, array('PAGE', 'SITE')))
                {
                    // Checking if the pageId is the current viewed
                    $flag = ($val->page_id == $pageId) ? 1 : 0;
                    $val->isActive = $flag;
            
                    array_push($breadcrumb, $val);
                }
            }
        }
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'breadcrumb' => $breadcrumb
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
