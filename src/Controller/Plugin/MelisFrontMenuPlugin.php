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
 * "Menu" plugin.
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
    public function __construct($updatesPluginConfig = array())
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'melisMenu';
        parent::__construct($updatesPluginConfig);
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $data = $this->getFormData();
        
        $pageId = !empty($data['pageIdRootMenu']) ? $data['pageIdRootMenu'] : 1;

        // Retrieve cache version if front mode to avoid multiple calls
		$cacheKey = 'MelisFrontMenuPlugin_'.$data['pageId'].'_'.$this->cleanString($data['id']). '_' .$this->cleanString($data['template_path']);
		$cacheConfig = 'melisfront_pages_file_cache';
		$melisEngineCacheSystem = $this->getServiceManager()->get('MelisEngineCacheSystem');
        $results = $melisEngineCacheSystem->getCacheByKey($cacheKey, $cacheConfig);

        if (!is_null($results))
            return $results;
        
        // Getting the Site Menu from MelisFrontNavigator
        $site = new MelisFrontNavigation($this->getServiceManager(), $pageId, $this->renderMode);

        $siteMenu = $site->getPageAndSubPages($pageId);
        $siteMenu = $this->checkValidPagesRecursive($siteMenu);
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $data['id'],
            'menu' => $siteMenu
        );

        // Save cache key
		$melisEngineCacheSystem->setCacheByKey($cacheKey, $cacheConfig, $viewVariables);

        // return the variable array and let the view be created
        return $viewVariables;
    }

    /**
     * This function generates the form displayed when editing the parameters of the plugin
     * @return array
     */
    public function createOptionsForms()
    {
        // construct form
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formConfig = $this->pluginBackConfig['modal_form'];

        $response = [];
        $render   = [];
        if (!empty($formConfig)) {
            foreach ($formConfig as $formKey => $config) {
                $form = $factory->createForm($config);
                $request = $this->getServiceManager()->get('request');
                $parameters = $request->getQuery()->toArray();

                if (!isset($parameters['validate'])) {

                    $form->setData($this->getFormData());
                    $viewModelTab = new ViewModel();
                    $viewModelTab->setTemplate($config['tab_form_layout']);
                    $viewModelTab->modalForm = $form;
                    $viewModelTab->formData   = $this->getFormData();
                    $viewRender = $this->getServiceManager()->get('ViewRenderer');
                    $html = $viewRender->render($viewModelTab);
                    array_push($render, [
                            'name' => $config['tab_title'],
                            'icon' => $config['tab_icon'],
                            'html' => $html
                        ]
                    );
                }
                else {

                    // validate the forms and send back an array with errors by tabs
                    $post = $request->getPost()->toArray();
                    $success = false;
                    $form->setData($post);

                    $errors = array();
                    if ($form->isValid()) {

                        $cacheKey = 'MelisFrontMenuPlugin_' . $this->getFormData()['id'];
                        $cacheConfig = 'melisfront_pages_file_cache';
                        $melisEngineCacheSystem = $this->getServiceManager()->get('MelisEngineCacheSystem');
                        $melisEngineCacheSystem->deleteCacheByPrefix($cacheKey, $cacheConfig);

                        $data = $form->getData();
                        $success = true;
                        array_push($response, [
                            'name' => $this->pluginBackConfig['modal_form'][$formKey]['tab_title'],
                            'success' => $success,
                        ]);
                    } else {
                        $errors = $form->getMessages();

                        foreach ($errors as $keyError => $valueError) {
                            foreach ($config['elements'] as $keyForm => $valueForm) {
                                if ($valueForm['spec']['name'] == $keyError &&
                                    !empty($valueForm['spec']['options']['label'])
                                )
                                    $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                            }
                        }


                        array_push($response, [
                            'name' => $this->pluginBackConfig['modal_form'][$formKey]['tab_title'],
                            'success' => $success,
                            'errors' => $errors,
                            'message' => '',
                        ]);
                    }

                }
            }
        }

        if (!isset($parameters['validate'])) {
            return $render;
        }
        else {
            return $response;
        }

    }

    /**
     * Returns the data to populate the form inside the modals when invoked
     * @return array|bool|null
     */
    public function getFormData()
    {
        $datas = parent::getFormData();

        return $datas;
    }

    /**
     * This method will decode the XML in DB to make it in the form of the plugin config file
     * so it can overide it. Only front key is needed to update.
     * The part of the XML corresponding to this plugin can be found in $this->pluginXmlDbValue
     */
    public function loadDbXmlToPluginConfig()
    {
        $configValues = array();

        $xml = simplexml_load_string($this->pluginXmlDbValue);
        if ($xml)
        {

            if (!empty($xml->template_path))
                $configValues['template_path'] = (string)$xml->template_path;

            if (!empty($xml->pageIdRootMenu))
                $configValues['pageIdRootMenu'] = (string)$xml->pageIdRootMenu;
        }

        return $configValues;
    }

    /**
     * This method saves the XML version of this plugin in DB, for this pageId
     * Automatically called from savePageSession listenner in PageEdition
     */
    public function savePluginConfigToXml($parameters)
    {
        $xmlValueFormatted = '';

        // template_path is mendatory for all plugins
        if (!empty($parameters['template_path']))
            $xmlValueFormatted .= "\t\t" . '<template_path><![CDATA[' . $parameters['template_path'] . ']]></template_path>';

        if(!empty($parameters['pageIdRootMenu']))
            $xmlValueFormatted .= "\t\t" . '<pageIdRootMenu><![CDATA[' . $parameters['pageIdRootMenu'] . ']]></pageIdRootMenu>';

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }
    
    /**
     * This method checks the show menu option of the page,
     */
    public function checkValidPagesRecursive($siteMenu = array())
    {
        $checkedSiteMenu = array();
        
        foreach($siteMenu as $key => $val){
            
            if($val['menu'] != 'NONE'){
                
                if($val['menu'] == 'NOLINK'){
                    $val['uri'] = '#';
                }
                
                if(!empty($val['pages'])){
                    
                   $pages  = $this->checkValidPagesRecursive($val['pages']);
                   
                   if(!empty($pages)){
                       $val['pages'] = $pages;
                   }
                }
                
                $checkedSiteMenu[] = $val;
            }
            
        }
        
        return $checkedSiteMenu;
    }
}
