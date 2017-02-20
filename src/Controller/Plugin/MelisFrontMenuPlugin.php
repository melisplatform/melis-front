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
 * "Menu" plugin.
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
 * $plugin = $this->MelisFrontMenuPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontMenuPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/menu/menu'
 * );
 * $pluginView = $plugin->render($parameters);
 * 
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'menu');
 * 
 * How to display in your controller's view:
 * echo $this->menu;
 * 
 * 
 */
class MelisFrontMenuPlugin extends MelisTemplatingPlugin
{
    // the key of the configuration in the app.plugins.php
    public $configPluginKey = 'melisfront';
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : null;
        
        // Getting the Site Menu from MelisFrontNavigator
        $site = new MelisFrontNavigation($this->getServiceLocator(), $pageId, $this->renderMode);
        $mainPageId = $site->getSiteMainPageByPageId($pageId);
        
        $siteMenu = $site->getSiteMenu($mainPageId);
        /**
         * Sending service end event
         * This process param can be modified by catching the event from listeners
         * To modified the data, need to use the same param name
         * in the sample code we use index "menu". and return same index of variable
         * Ex.
         *      array['menu'] = modifiedArray(datas....);
         */
            
        $siteMenu = $this->sendEvent('melisfront_site_menu_plugin', array('menu' => $siteMenu));
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'menu' => $siteMenu['menu']
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
