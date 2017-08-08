<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
/**
 * This plugin implements the business logic of the
 * "SearchResults" plugin.
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
 * $plugin = $this->MelisFrontSearchResultsPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontSearchResultsPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/search/search-results'
 * );
 * $pluginView = $plugin->render($parameters);
 * 
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'searchresults');
 * 
 * How to display in your controller's view:
 * echo $this->searchresults;
 * 
 * 
 */
class MelisFrontSearchResultsPlugin extends MelisTemplatingPlugin
{
    public function __construct($updatesPluginConfig = array())
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'MelisFrontSearchResultsPlugin';
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
        
        // Plugin properties
        $pageId     = (!empty($data['pageId']))         ? $data['pageId']           : null;
        $moduleName = (!empty($data['siteModuleName'])) ? $data['siteModuleName']   : getenv('MELIS_MODULE');
        $keyword    = (!empty($data['keyword']))        ? $data['keyword']          : null;
        
        // Pagination
        $current                = (!empty($data['current']))            ? $data['current']              : 1;
        $nbPerPage              = (!empty($data['nbPerPage']))          ? $data['nbPerPage']            : 10;
        $nbPageBeforeAfter      = (!empty($data['nbPageBeforeAfter']))  ? $data['nbPageBeforeAfter']    : 0;
        
        $moduleDirWritable = true;

        $indexUrl = '';
        $isIndex = false;
        $siteDirExist = true;
        
        if (!is_null($moduleName))
        {
            if (is_dir($_SERVER['DOCUMENT_ROOT'].'/../module/MelisSites/'.$moduleName))
            {
                if (file_exists($_SERVER['DOCUMENT_ROOT'].'/../module/MelisSites/'.$moduleName.'/luceneIndex/indexes'))
                {
                    $isIndex = true;
                    $indexUrl = '';
                }
                else
                {
                    if (!is_writable($_SERVER['DOCUMENT_ROOT'].'/../module/MelisSites/'.$moduleName.'/luceneIndex/'))
                    {
                        $moduleDirWritable = false;
                    }
                    else
                    {
                        
                        
                        if (is_null($pageId))
                        {
                            /**
                             * Getting the current page id
                             */
                            $renderMode = $this->getController()->params()->fromRoute('renderMode');
                            
                            if ($renderMode == 'front')
                            {
                                $pageId = $this->getController()->params()->fromRoute('idpage');
                            }
                            else
                            {
                                $pageId = $data['pageId'];
                            }
                            
                        }
                        
                        /**
                         * Indexing Site will use the main page of the Site
                         */
                        $pageTreeSrv = $this->getServiceLocator()->get('MelisEngineTree');
                        $pageSite = $pageTreeSrv->getSiteByPageId($pageId);
                
                        $mainPageId = 1;
                        if (!is_null($pageSite))
                        {
                            $mainPageId = $pageSite->site_main_page_id;
                        }
                
                        // Get the current server protocol
                        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
                
                        $indexUrl = $protocol.$_SERVER['SERVER_NAME'].'/melissearchindex/module/'.$moduleName.'/pageid/'.$mainPageId.'/exclude-pageid/0';
                    }
                }
            }
            else 
            {
                $siteDirExist = false;
            }
        }
        
        $searchresults = array();
        if ($isIndex && $keyword)
        {
            $searchSvc = $this->getServiceLocator()->get('MelisSearch');
            $searchresults = $searchSvc->search($keyword, $moduleName, true);
            if (!empty($searchresults))
            {
                $searchresults = str_replace('&', '&amp;', $searchresults);
                
                $searchresults = (Array) simplexml_load_string($searchresults);
                $searchresults = (!empty($searchresults['result'])) ? $searchresults['result'] : array();
                
                // Checking if the Search array result is multidimensional array or single array
                if (is_object($searchresults))
                {
                    // Making the search result to be multidimensional array
                    $temp[] = $searchresults;
                    $searchresults = $temp;
                }
            }
        }
        
        $paginator = new Paginator(new ArrayAdapter($searchresults));
        $paginator->setCurrentPageNumber($current)
                    ->setItemCountPerPage($nbPerPage)
                    ->setPageRange(($nbPageBeforeAfter*2) + 1);
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $this->pluginFrontConfig['id'],
            'moduleName' => $moduleName,
            'indexerOk' => $isIndex,
            'indexerURL' => $indexUrl,
            'moduleDirWritable' => $moduleDirWritable,
            'searchresults' => $paginator,
            'nbPageBeforeAfter' => $nbPageBeforeAfter,
            'siteDirExist' => $siteDirExist,
            'searchKey' => $keyword,
        );
        
        // return the variable array and let the view be created
        return $viewVariables;
    }
    
    /**
     * Removes invalid XML
     *
     * @access public
     * @param string $value
     * @return string
     */
    private function stripInvalidXml($value)
    {
        $ret = "";
        $current;
        if (empty($value))
        {
            return $ret;
        }
        
        $length = strlen($value);
        for ($i=0; $i < $length; $i++)
        {
            $current = ord($value{$i});
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF)))
            {
                $ret .= chr($current);
            }
            else
            {
                $ret .= " ";
            }
        }
        return $ret;
    }

    /**
     * This function generates the form displayed when editing the parameters of the plugin
     * @return array
     */
    public function createOptionsForms()
    {
        $this->loadDbXmlToPluginConfig();
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
        $data = parent::getFormData();
        return $data;
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

            if (!empty($xml->siteModuleName))
                $configValues['siteModuleName'] = (string)$xml->siteModuleName;

            if (!empty($xml->keyword))
                $configValues['keyword'] = (string)$xml->keyword;

            if (!empty($xml->current))
                $configValues['current'] = (string)$xml->current;

            if (!empty($xml->nbPerPage))
                $configValues['nbPerPage'] = (string)$xml->nbPerPage;

            if (!empty($xml->nbPageBeforeAfter))
                $configValues['nbPageBeforeAfter'] = (string)$xml->nbPageBeforeAfter;
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
        if(!empty($parameters['siteModuleName']))
            $xmlValueFormatted .= "\t\t" . '<siteModuleName><![CDATA['   . $parameters['siteModuleName'] . ']]></siteModuleName>';
        if(!empty($parameters['keyword']))
            $xmlValueFormatted .= "\t\t" . '<keyword><![CDATA['   . $parameters['keyword'] . ']]></keyword>';
        if(!empty($parameters['current']))
            $xmlValueFormatted .= "\t\t" . '<current><![CDATA['   . $parameters['current'] . ']]></current>';
        if(!empty($parameters['nbPerPage']))
            $xmlValueFormatted .= "\t\t" . '<nbPerPage><![CDATA['   . $parameters['nbPerPage'] . ']]></nbPerPage>';
        if(!empty($parameters['nbPageBeforeAfter']))
            $xmlValueFormatted .= "\t\t" . '<nbPageBeforeAfter><![CDATA['   . $parameters['nbPageBeforeAfter'] . ']]></nbPageBeforeAfter>';

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }
}