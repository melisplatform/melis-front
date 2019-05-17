<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

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
    private const MELIS_SITES = '/../module/MelisSites/';
    private const VENDOR = '/../vendor/melisplatform/';
    private const SEARCH_PATH_MELIS_SITES = 'module/MelisSites/';
    private const SEARCH_PATH_VENDOR = 'vendor/melisplatform/';

    public function __construct($updatesPluginConfig = [])
    {
        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'MelisFrontSearchResultsPlugin';
        parent::__construct($updatesPluginConfig);
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     * @return array
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $data = $this->getFormData();

        // Plugin properties
        $pageId = empty($data['pageId']) ? null : $data['pageId'];
        $moduleName = empty($data['siteModuleName']) ? getenv('MELIS_MODULE') : $data['siteModuleName'];
        $keyword = empty($data['keyword']) ? null : $data['keyword'];

        // Pagination
        $current = empty($data['current']) ? 1 : $data['current'];
        $nbPerPage = empty($data['nbPerPage']) ? 10 : $data['nbPerPage'];
        $nbPageBeforeAfter = empty($data['nbPageBeforeAfter']) ? 0 : $data['nbPageBeforeAfter'];

        $moduleDirWritable = true;
        $indexUrl = '';
        $isIndex = false;
        $siteDirExist = true;

        $moduleDirectory = '';
        $searchModulePath = self::SEARCH_PATH_MELIS_SITES;
        if (!is_null($moduleName)) {
            /**
             * Folder names inside vendor/melisplatform is in lowercase-delimited-by-hyphens
             * Ex. $vendorSiteName = 'melis-demo-cms';
             */
            $vendorSiteName = preg_split('/(?=[A-Z])/', $moduleName, null, PREG_SPLIT_NO_EMPTY);
            $vendorSiteName = array_map('strtolower', $vendorSiteName);
            $vendorSiteName = implode('-', $vendorSiteName);

            if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::MELIS_SITES . $moduleName)) {
                /** Module is located inside MelisSites folder. Ex. $moduleName = "MelisDemoCms" */
                $moduleDirectory = $_SERVER['DOCUMENT_ROOT'] . self::MELIS_SITES . $moduleName;
            } elseif (is_dir($_SERVER['DOCUMENT_ROOT'] . self::VENDOR . $vendorSiteName)) {
                /** Module is located inside Vendor folder. Ex. $moduleName = "melis-demo-cms" */
                $moduleDirectory = $_SERVER['DOCUMENT_ROOT'] . self::VENDOR . $vendorSiteName;
                $searchModulePath = self::SEARCH_PATH_VENDOR;
            } else {
                $siteDirExist = false;
            }

            if ($siteDirExist === true) {
                if (file_exists($moduleDirectory . '/luceneIndex/indexes')) {
                    $isIndex = true;
                    $indexUrl = '';
                } else {
                    if (is_writable($moduleDirectory . '/luceneIndex/')) {
                        if (is_null($pageId)) {
                            /**
                             * Getting the current page id
                             */
                            $renderMode = $this->getController()->params()->fromRoute('renderMode');

                            if ($renderMode == 'front') {
                                $pageId = $this->getController()->params()->fromRoute('idpage');
                            } else {
                                $pageId = $data['pageId'];
                            }
                        }

                        /**
                         * Indexing Site will use the main page of the Site
                         */
                        $pageTreeSrv = $this->getServiceLocator()->get('MelisEngineTree');
                        $pageSite = $pageTreeSrv->getSiteByPageId($pageId);
                        $mainPageId = empty($pageSite->site_main_page_id) ? 1 : $pageSite->site_main_page_id;

                        // Get the current server protocol
                        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

                        $indexUrl = $protocol . $_SERVER['SERVER_NAME'] . '/melissearchindex/module/' . $moduleName . '/pageid/' . $mainPageId . '/exclude-pageid/0';
                    } else {
                        $moduleDirWritable = false;
                    }
                }
            }
        }

        $searchresults = [];
        if ($isIndex && $keyword) {
            /** @var \MelisEngine\Service\MelisSearchService $searchSvc */
            $searchSvc = $this->getServiceLocator()->get('MelisSearch');
            $searchresults = $searchSvc->search($keyword, $moduleName, true, $searchModulePath);
            if (!empty($searchresults)) {
                $searchresults = str_replace('&', '&amp;', $searchresults);

                $searchresults = (Array)simplexml_load_string($searchresults);
                $searchresults = (!empty($searchresults['result'])) ? $searchresults['result'] : [];

                // Checking if the Search array result is multidimensional array or single array
                if (is_object($searchresults)) {
                    // Making the search result to be multidimensional array
                    $temp[] = $searchresults;
                    $searchresults = $temp;
                }
            }
        }

        $paginator = new Paginator(new ArrayAdapter($searchresults));
        $paginator->setCurrentPageNumber($current)
            ->setItemCountPerPage($nbPerPage)
            ->setPageRange(($nbPageBeforeAfter * 2) + 1);

        // Create an array with the variables that will be available in the view
        $viewVariables = [
            'pluginId' => $this->pluginFrontConfig['id'],
            'moduleName' => $moduleName,
            'indexerOk' => $isIndex,
            'indexerURL' => $indexUrl,
            'moduleDirWritable' => $moduleDirWritable,
            'searchresults' => $paginator,
            'nbPageBeforeAfter' => $nbPageBeforeAfter,
            'siteDirExist' => $siteDirExist,
            'searchKey' => $keyword,
        ];

        // return the variable array and let the view be created
        return $viewVariables;
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
        $render = [];
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
                    $viewModelTab->formData = $this->getFormData();
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModelTab);
                    array_push($render, [
                            'name' => $config['tab_title'],
                            'icon' => $config['tab_icon'],
                            'html' => $html
                        ]
                    );
                } else {

                    // validate the forms and send back an array with errors by tabs
                    $post = get_object_vars($request->getPost());
                    $success = false;
                    $form->setData($post);

                    if ($form->isValid()) {
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
        } else {
            return $response;
        }

    }

    /**
     * This method will decode the XML in DB to make it in the form of the plugin config file
     * so it can overide it. Only front key is needed to update.
     * The part of the XML corresponding to this plugin can be found in $this->pluginXmlDbValue
     */
    public function loadDbXmlToPluginConfig()
    {
        $configValues = [];

        $xml = simplexml_load_string($this->pluginXmlDbValue);
        if ($xml) {
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
        if (!empty($parameters['siteModuleName']))
            $xmlValueFormatted .= "\t\t" . '<siteModuleName><![CDATA[' . $parameters['siteModuleName'] . ']]></siteModuleName>';
        if (!empty($parameters['keyword']))
            $xmlValueFormatted .= "\t\t" . '<keyword><![CDATA[' . $parameters['keyword'] . ']]></keyword>';
        if (!empty($parameters['current']))
            $xmlValueFormatted .= "\t\t" . '<current><![CDATA[' . $parameters['current'] . ']]></current>';
        if (!empty($parameters['nbPerPage']))
            $xmlValueFormatted .= "\t\t" . '<nbPerPage><![CDATA[' . $parameters['nbPerPage'] . ']]></nbPerPage>';
        if (!empty($parameters['nbPageBeforeAfter']))
            $xmlValueFormatted .= "\t\t" . '<nbPageBeforeAfter><![CDATA[' . $parameters['nbPageBeforeAfter'] . ']]></nbPageBeforeAfter>';

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }
}
