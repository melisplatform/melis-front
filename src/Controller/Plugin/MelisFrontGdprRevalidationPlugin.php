<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;


use MelisCore\Service\MelisCoreGdprAutoDeleteService;
use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use MelisEngine\Service\MelisGdprAutoDeleteService;
use MelisFront\Service\MelisSiteConfigService;
use Laminas\Db\Sql\Sql;
use Laminas\Form\Factory;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;

/**
 * This plugin implements the business logic of the
 * "latestBlog" plugin.
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
 * $plugin = $this->MelisFrontGdprRevalidationPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontGdprRevalidationPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/cms/gdprBanner'
 * );
 * $pluginView = $plugin->render($parameters);
 *
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'gdprBanner');
 *
 * How to display in your controller's view:
 * echo $this->gdpr-revalidation;
 *
 *
 */
class MelisFrontGdprRevalidationPlugin extends MelisTemplatingPlugin
{
    /**
     * @var
     */
    protected $errors;

    /**
     * @var
     */
    protected $service;

    /**
     * MelisFrontGdprRevalidationPlugin constructor.
     * @param array $updatesPluginConfig
     */
    public function __construct($updatesPluginConfig = array())
    {
        parent::__construct($updatesPluginConfig);

        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'MelisFrontGdprRevalidation';
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        $locale = 'en_EN';
        $revalidated = false;
        $userStillActive = false;
        /** @var MelisGdprAutoDeleteService $gdprAutoDeleteService */
        $gdprAutoDeleteService = $this->getServiceManager()->get('MelisGdprAutoDeleteService');
        $pluginData = $this->getFormData();
        // request
        $request = $this->getServiceManager()->get('request');
        // get user data and check if user is valid
        $userData = $this->verifyUser($request, $pluginData);
        $revalidationForm = $this->getRevalidationForm($this->pluginFrontConfig['forms']['gdpr_revalidation_form']);
        $formError = [];
        if (! empty($userData)) {
            // check the gdpr_last date if it's already validated or days of inactivity is less than to the set auto delete alert email days
            $userDaysOfInactive = $gdprAutoDeleteService->getDaysDiff($userData->config['last_date'], date('Y-m-d'));
            if ($userDaysOfInactive < $this->queryNumberOfDaysInactive($pluginData)) {
                $userStillActive = true;
            }
            // revalidate user
            if ($request->isPost() && !$userStillActive) {
                $post = $request->getPost()->toArray();
                // set data
                if ($post['revalidate_account']) {
                    // update user status by the services from modules
                    $id = $this->service->updateGdprUserStatus($request->getQuery('u'));
                    if ($id) {
                        $revalidated = true;
                        // remove entry after revalidation
                        $this->removeUserEntryOnDeleteSentEmail($id);
                    }
                } else {
                    // return error
                    $formError = "tr_melis_front_gdpr_revalidation_not_checked";
                }
            }
        }


        $viewVariables = [
            'formData'         => $pluginData,
            'revalidationForm' => $revalidationForm,
            'userData'         => $userData,
            'isRevalidated'    => $revalidated,
            'userStillActive'  => $userStillActive,
            'error'           => $formError
        ];

        return $viewVariables;
    }

    /**
     * @param $pluginData
     * @return mixed
     */
    private function queryNumberOfDaysInactive($pluginData)
    {
        // get a table
        $table = $this->getServiceManager()->get('MelisEngineTablePlatformIds');
        // get table gateway
        $tableGateway = $table->getTableGateway();
        // set table
        $tableGateway->getSql()->setTable('melis_core_gdpr_delete_config');
        // query to table
        $select = $tableGateway->getSql()->select()->where('mgdprc_site_id = ' . $pluginData['site_id'])->where('mgdprc_module_name = "' . $pluginData['module'] . '"');
        // execute and get data
        $data = $tableGateway->getSql()->prepareStatementForSqlObject($select)->execute()->current();

        return $data['mgdprc_alert_email_days'];
    }

    /**
     * remove data entry in table melis_core_gdpr_delete_emails_sent
     * @param $validationKey
     */
    private function removeUserEntryOnDeleteSentEmail($validationKey)
    {
        return $this->getServiceManager()->get('MelisGdprDeleteEmailsSentTable')->deleteByField('mgdprs_account_id', $validationKey);
    }

    /**
     * create form
     * @param $formConfig
     * @return \Laminas\Form\ElementInterface
     */
    private function getRevalidationForm($formConfig)
    {
        // get form element manager
        $formElement = $this->getServiceManager()->get('FormElementManager');
        // get form factory class
        $factory      = new \Laminas\Form\Factory();
        // set form element manager
        $factory->setFormElementManager($formElement);

       return $factory->createForm($formConfig);
    }

    /**
     * @param $request
     * @param $pluginData
     * @return mixed
     */
    private function verifyUser($request, $pluginData)
    {
        $userData = null;
        $pluginData = $this->getFormData();
        /** @var MelisGdprAutoDeleteService $gdprAutoDeleteService */
        $gdprAutoDeleteService = $this->getServiceManager()->get('MelisGdprAutoDeleteService');
        // add config data in the session so modules can access on it
        $container = new Container('melis_auto_delete_gdpr');
        $container['config'] = (array) $gdprAutoDeleteService->getAutoDeleteConfig($pluginData['site_id'], $pluginData['module']);
        // get service class
        $service = $gdprAutoDeleteService->getServiceClassByModule($pluginData['module']);
        // get the auto delete config
        
        // if service class is set
        if (!empty($service)) {
            $service = $this->getServiceManager()->get($service);
            // check if method exists
            if (in_array('getUserPerValidationKey', get_class_methods($service))) {
                // return user data
                $userData = $service->getUserPerValidationKey($request->getQuery('u'));
                // set service for later uses
                $this->service = $service;

            } else {
                $this->errors[] =  "Method getUserPerValidationKey is missing on the service class";
            }
        } else {
            $this->errors[] = "Service not set";
        }

        return $userData;
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
     */
    public function createOptionsForms()
    {
        // construct form
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formConfig = $this->pluginBackConfig['modal_form'];
        $tool = $this->getServiceManager()->get('translator');
        $data = $this->getFormData();
        $response = [];
        $render = [];
        if (empty($data['site_id'])){
            $pageSvc = $this->getServiceManager()->get('MelisEnginePage');
            // get page data in published first
            $pageData = $pageSvc->getDatasPage($data['pageId']);
            // check page status if it is published
            if ($pageData->getMelisPageTree()->page_status) {
                // ste site id
                $data['site_id'] = $pageData->getMelisTemplate()->tpl_site_id;
            } else {
                // get page data in saved table
                $pageData = $pageSvc->getDatasPage($data['pageId'],'saved');
                // set site id 
                $data['site_id'] = $pageData->getMelisTemplate()->tpl_site_id;
            } 
        } 
        if (!empty($formConfig)) {
            foreach ($formConfig as $formKey => $config) {
                $form = $factory->createForm($config);
                $request = $this->getServiceManager()->get('request');
                $parameters = $request->getQuery()->toArray();

                if (!isset($parameters['validate'])) {
                    $form->setData($data);
                    $viewModelTab = new ViewModel();
                    $viewModelTab->setTemplate($config['tab_form_layout']);
                    $viewModelTab->modalForm = $form;
                    $viewModelTab->formData = $this->getFormData();

                    $viewModelTab->labels = [
                        'noPropsMsg' => $tool->translate('tr_melis_cms_gdpr_banner_plugin_empty_props'),
                    ];

                    $viewRender = $this->getServiceManager()->get('ViewRenderer');
                    $html = $viewRender->render($viewModelTab);
                    array_push($render, [
                            'name' => $config['tab_title'],
                            'icon' => $config['tab_icon'],
                            'html' => $html
                        ]
                    );
                } else {
                    // validate the forms and send back an array with errors by tabs
                    $post = $request->getPost()->toArray();
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
                                if ($valueForm['spec']['name'] == $keyError && !empty($valueForm['spec']['options']['label'])) {
                                    $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                                }
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
        $configValues = array();

        $xml = simplexml_load_string($this->pluginXmlDbValue);
        if ($xml) {
            if (!empty($xml->template_path)) {
                $configValues['template_path'] = (string)$xml->template_path;
            }
            if (!empty($xml->site_id)) {
                $configValues['site_id'] = (string)$xml->site_id;
            }
            if (!empty($xml->module)) {
                $configValues['module'] = (string)$xml->module;
            }
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
        if (!empty($parameters['site_id']))
            $xmlValueFormatted .= "\t\t" . '<site_id><![CDATA[' . $parameters['site_id'] . ']]></site_id>';
        if (!empty($parameters['module']))
            $xmlValueFormatted .= "\t\t" . '<module><![CDATA[' . $parameters['module'] . ']]></module>';

        // for resizing
        $widthDesktop = null;
        $widthMobile   = null;
        $widthTablet  = null;

        if (! empty($parameters['melisPluginDesktopWidth'])) {
            $widthDesktop =  " width_desktop=\"" . $parameters['melisPluginDesktopWidth'] . "\" ";
        }
        if (! empty($parameters['melisPluginMobileWidth'])) {
            $widthMobile =  "width_mobile=\"" . $parameters['melisPluginMobileWidth'] . "\" ";
        }
        if (! empty($parameters['melisPluginTabletWidth'])) {
            $widthTablet =  "width_tablet=\"" . $parameters['melisPluginTabletWidth'] . "\" ";
        }

        //
        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '"' .$widthDesktop . $widthMobile . $widthTablet . ' >' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }
}
