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
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
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
class MelisFrontBreadcrumbPlugin extends MelisTemplatingPlugin
{
    
    public function __construct($updatesPluginConfig = array())
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'melisBreadcrumb';
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
        
        // Retrieving the pageId from config
        $pageId = (!empty($data['pageIdRootBreadcrumb'])) ? $data['pageIdRootBreadcrumb'] : null;
        
        $treeSrv = $this->getServiceLocator()->get('MelisEngineTree');
        $pageSrv = $this->getServiceLocator()->get('MelisEnginePage');
      //  $pageBreadcrumb = $treeSrv->getPageBreadcrumb($pageId, 0, true);
        $pageData = $pageSrv->getDatasPage($pageId);
        $pageData = (array)$pageData->getMelisPageTree();

        $flag = ($pageData['page_id'] == $pageId) ? 1 : 0;
        //Incase if there is no more pageChildren
        $tmp2 = array(
            'label' =>  (!empty($pageData['pseo_meta_title'])) ? "<b>" .$pageData['pseo_meta_title'] . "</b>" : "<b>" . $pageData['page_name'] ."</b>" ,
            'idPage' => $pageData['page_id'],
            'isActive' => $flag,
        );

       // $pageBreadcrumb = $treeSrv->getAllPages($pageId);
        //$pageBreadcrumb = $this->getBreadCrumbsRec($pageBreadcrumb['children'],$pageId);
        $pageBreadcrumb = $treeSrv->getPageChildren($pageId)->toArray();
        $breadcrumb = array();
       // $breadcrumb = $pageBreadcrumb;

        if (is_array($pageBreadcrumb))
        {
            foreach ($pageBreadcrumb As $key => $val)
            {
                if (in_array($val['page_type'], array('PAGE', 'SITE')))
                {
                    // Checking if the pageId is the current viewed
                    $flag = ($val['page_id'] == $pageId) ? 1 : 0;

                    $label = (!empty($val['pseo_meta_title'])) ? $val['pseo_meta_title'] : $val['page_name'];
                    $tmp = array(
                        'label' => $label,
                        'menu' => $val['page_menu'],
                        'uri' => $treeSrv->getPageLink($val['page_id'], false),
                        'idPage' => $val['page_id'],
                        'lastEditDate' => $val['page_edit_date'],
                        'pageStat' => $val['page_status'],
                        'pageType' => $val['page_type'],
                        'isActive' => $flag,
                    );

                    array_push($breadcrumb, $tmp);
                }
            }
        }

        //Add the current page
        array_unshift($breadcrumb, $tmp2);

        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $data['id'],
            'breadcrumb' => $breadcrumb,
        );
        
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
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formConfig = $this->pluginBackConfig['modal_form'];

        $response = [];
        $render   = [];
        if (!empty($formConfig)) {
            foreach ($formConfig as $formKey => $config) {
                $form = $factory->createForm($config);
                $request = $this->getServiceLocator()->get('request');
                $parameters = $request->getQuery()->toArray();

                if (!isset($parameters['validate'])) {

                    $form->setData($this->getFormData());
                    $viewModelTab = new ViewModel();
                    $viewModelTab->setTemplate($config['tab_form_layout']);
                    $viewModelTab->modalForm = $form;
                    $viewModelTab->formData   = $this->getFormData();
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
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
                    $post = get_object_vars($request->getPost());
                    $success = false;
                    $form->setData($post);

                    $errors = array();
                    if ($form->isValid()) {
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
        return parent::getFormData();
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
            if (!empty($xml->pageIdRootBreadcrumb))
                $configValues['pageIdRootBreadcrumb'] = (string)$xml->pageIdRootBreadcrumb;
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
        if (!empty($parameters['pageIdRootBreadcrumb']))
            $xmlValueFormatted .= "\t\t" . '<pageIdRootBreadcrumb><![CDATA[' . $parameters['pageIdRootBreadcrumb'] . ']]></pageIdRootBreadcrumb>';
          
        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }

    private function getBreadCrumbsRec($pages, $pageId, $data = [])
    {
        $treeSrv = $this->getServiceLocator()->get('MelisEngineTree');

        foreach($pages as $pageData => $val)
        {
            $flag = ($val['page_id'] == $pageId) ? 1 : 0;
            $label = (!empty($val['pseo_meta_title'])) ? $val['pseo_meta_title'] : $val['page_name'];

            $data[] = array(
                'label'        => $label,
                'menu'         => $val['page_menu'],
                'uri'          => $treeSrv->getPageLink($val['page_id'], false),
                'idPage'       => $val['page_id'],
                'lastEditDate' => $val['page_edit_date'],
                'pageStat'     => $val['page_status'],
                'pageType'     => $val['page_type'],
                'isActive'     => $flag,
            );

            if(isset($val['children'])){
                $data = $this->getBreadCrumbsRec($val['children'], $pageId, $data);
            }
        }

        return $data;
    }

}
