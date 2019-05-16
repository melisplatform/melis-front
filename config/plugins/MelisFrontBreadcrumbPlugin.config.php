<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontBreadcrumbPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/breadcrumb'),
                        'id' => 'breadcrumb',
                        'pageIdRootBreadcrumb' => 1,
                        
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                            ),
                        ),
                    ),
                    'melis' => array(
                        'subcategory' => array(
                            'id' => 'BASICS',
                            'title' => 'tr_MelisFrontSubcategoryPageBasics_Title'
                        ),
                        'name' => 'tr_MelisFrontBreadcrumbPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontBreadcrumbPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontBreadcrumbPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                            ),
                        ),
                        'js_initialization' => array(),
                        /*
                         * if set this plugin will belong to a specific marketplace section,
                         * if not it will go directly to ( Others ) section
                         *  - available section for templating plugins as of 2019-05-16
                         *    - MelisCms
                         *    - MelisMarketing
                         *    - MelisSite
                         *    - Others
                         *    - CustomProjects
                         */
                        'section' => 'MelisCms',
                        'modal_form' => array(
                            'breadcrumb_tab1' => array(
                                'tab_title' => 'tr_front_plugin_tab_properties',
                                'tab_icon'  => 'fa fa-cog',
                                'tab_form_layout' => 'MelisFront/breadcrumb/melis/form',
                                'attributes' => array(
                                    'name' => 'breadcrumb_tab1',
                                    'id' => 'breadcrumb_tab1',
                                    'method' => '',
                                    'action' => '',
                                ),
                                'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'name' => 'template_path',
                                            'type' => 'MelisEnginePluginTemplateSelect',
                                            'options' => array(
                                                'label' => 'tr_melis_Plugins_Template',
                                                'tooltip' => 'tr_melis_Plugins_Template tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ),
                                            'attributes' => array(
                                                'id' => 'id_page_tpl_id',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'pageIdRootBreadcrumb',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_breadcrumb_root_page',
                                                'tooltip' => 'tr_front_plugin_breadcrumb_root_page tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'pageIdRootBreadcrumb',
                                                'class' => 'melis-input-group-button',
                                                'data-button-icon' => 'fa fa-sitemap',
                                                'data-button-id' => 'meliscms-site-selector',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                ),
                                'input_filter' => array(
        					        'template_path' => array(
        								'name'     => 'template_path',
        								'required' => true,
        								'validators' => array(
        								    array(
        								        'name' => 'NotEmpty',
        								        'options' => array(
        								            'messages' => array(
        								                \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
        								            ),
        								        ),
        								    ),
        								),
        								'filters'  => array(
        								),
        					        ),
                                    'pageIdRootBreadcrumb' => array(
                                        'name'     => 'pageIdRootBreadcrumb',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name'    => 'Digits',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Zend\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Zend\Validator\Digits::STRING_EMPTY => '',
                                                    ),
                                                ),
                                            ),
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
             ),
        ),
     ),
);