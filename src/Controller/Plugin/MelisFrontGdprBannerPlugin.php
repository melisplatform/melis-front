<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;


use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Laminas\Form\Factory;
use Laminas\View\Model\ViewModel;

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
 * $plugin = $this->MelisFrontGdprBannerPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontGdprBannerPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/cms/gdprBanner'
 * );
 * $pluginView = $plugin->render($parameters);
 *
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'gdprBanner');
 *
 * How to display in your controller's view:
 * echo $this->gdprBanner;
 *
 *
 */
class MelisFrontGdprBannerPlugin extends MelisTemplatingPlugin
{
    public function __construct($updatesPluginConfig = array())
    {
        parent::__construct($updatesPluginConfig);

        $this->configPluginKey = 'melisfront';
        $this->pluginXmlDbKey = 'MelisFrontGdprBanner';
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        $locale = 'en_EN';
        $data = $this->getFormData();
        $translator = $this->getServiceManager()->get('translator');
        /**
         * Get site & language from the page id
         * @var \MelisEngine\Service\MelisPageService $pageService
         */
        $langId = null;
        $siteId = null;
        $pageId = empty($data['pageId']) ? null : $data['pageId'];
        if (!empty($pageId)) {
            $pageService = $this->getServiceManager()->get('MelisEnginePage');
            $pageData = $pageService->getDatasPage($pageId, 'saved')->getMelisPageTree();
            if (!empty($pageData)) {
                $langId = empty($pageData->lang_cms_id) ? $langId : $pageData->lang_cms_id;
                $locale = empty($pageData->lang_cms_locale) ? $locale : $pageData->lang_cms_locale;
                if (!empty($pageData->page_tpl_id)) {
                    /** @var \MelisEngine\Model\Tables\MelisTemplateTable $tplTable */
                    $tplTable = $this->getServiceManager()->get('MelisEngineTableTemplate');
                    $tplData = $tplTable->getEntryById($pageData->page_tpl_id)->toArray();
                    if (!empty($tplData [0]['tpl_site_id'])) {
                        $siteId = $tplData [0]['tpl_site_id'];
                    }
                }
            }
        }

        /**
         * Get banner content for this site & language
         * @var \MelisEngine\Service\MelisGdprService $bannerService
         */
        $bannerService = $this->getServiceManager()->get('MelisGdprService');
        $bannerContents = $bannerService->getGdprBannerText((int)$siteId, (int)$langId)->toArray();
        if (!empty($bannerContents[0])) {
            $bannerContents = $bannerContents[0]['mcgdpr_text_value'];
        } else {
            $bannerContents = "";
        }

        $labels = [
            'pluginLoaded' => $translator->translate('tr_melis_cms_gdpr_banner_plugin_loaded'),
            'agree' => $translator->translate('tr_melis_front_gdpr_banner_agree_' . $locale),
        ];

        $viewVariables = [
            'labels' => $labels,
            'pluginId' => $data['id'],
            'isInBackOffice' => $this->isInBackOffice(),
            'bannerContents' => $bannerContents,
        ];

        return $viewVariables;
    }

    /**
     * @return bool
     */
    private function isInBackOffice()
    {
        $request = $this->getController()->getRequest();
        $routeMatch = $this->getServiceManager()->get('router')->match($request);
        $routeName = $routeMatch->getMatchedRouteName();
        $module = explode('/', $routeName);

        /**
         * Page edition: melis_front_melisrender
         */
        if (is_int(array_search('melis_front_melisrender', $module))) {
            return true;
        }

        /**
         * Page preview: melis_front_previewender
         */
        if (is_int(array_search('melis_front_previewender', $module))) {
            return false;
        }

        return false;
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

        $response = [];
        $render = [];
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

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }
}
