<?php

namespace MelisFront\Controller\Plugin;


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
 * $plugin = $this->MiniTemplatePlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MiniTemplatePlugin();
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
class MiniTemplatePlugin extends MelisFrontTagPlugin
{
    public function __construct()
    {
        $this->configPluginKey = 'MelisMiniTemplate';
        $this->pluginXmlDbKey = 'melisTag';
    }

    /**
     * This function will set the plugin name
     *
     * @param $pluginName
     */
    public function setMiniTplPluginName($pluginName){
        $this->pluginName = $pluginName;
    }
}