<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontSearchResultsPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/search-results'),
                        'id' => 'search-results',
                        'pageId' => null,
                        'siteModuleName' => null,
                        'keyword' => null,
                        // optional, if found will add a pagination object
                        'pagination' => array(
                            'current' => 1,
                            'nbPerPage' => 10,
                            'nbPageBeforeAfter' => 0
                        ),
                        
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
                        'name' => 'tr_MelisFrontSearchResultsPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontSearchResultsPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontSearchResultsPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                            ),
                        ),
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
                        'js_initialization' => array(),
                        'modal_form' => array(
                            'plugin_search_tab_1' => array(
                                'tab_title' => 'tr_front_plugin_tab_properties',
                                'tab_icon'  => 'fa fa-cog',
                                'tab_form_layout' => 'MelisFront/search/melis/form',
                                'attributes' => array(
                                    'name' => 'search_plugin_tab_1',
                                    'id' => 'search_plugin_tab_1',
                                    'method' => '',
                                    'action' => '',
                                ),
                                'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
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
                                            'name' => 'siteModuleName',
                                            'type' => 'MelisCmsPluginSiteModuleSelect',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_search_site',
                                                'tooltip' => 'tr_front_plugin_search_site tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ),
                                            'attributes' => array(
                                                'id' => 'siteModuleName',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'keyword',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_search_site_keyword',
                                                'tooltip' => 'tr_front_plugin_search_site_keyword tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'keyword',
                                                'class' => 'form-control',
                                            ),
                                        ),
                                    )
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
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                    'siteModuleName' => array(
                                        'name'     => 'siteModuleName',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                            array('name' => 'StripTags'),
                                            array('name' => 'StringTrim'),
                                        ),
                                    ),
                                ),
                            ),
                            'plugin_search_tab_2' => array(
                                'tab_title' => 'tr_front_plugin_search_pagination',
                                'tab_icon'  => 'fa fa-forward',
                                'tab_form_layout' => 'MelisFront/search/melis/form',
                                'attributes' => array(
                                    'name' => 'search_plugin_tab_2',
                                    'id' => 'search_plugin_tab_2',
                                    'method' => '',
                                    'action' => '',
                                ),
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'name' => 'current',
                                            'type' => 'hidden',
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'nbPerPage',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_search_pagination_nbPerPage',
                                                'tooltip' => 'tr_front_plugin_search_pagination_nbPerPage tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'nbPerPage',
                                                'class' => 'form-control',
                                                'placeholder' => 'tr_front_plugin_search_pagination_nbPerPage',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'nbPageBeforeAfter',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_search_pagination_nbPageBeforeAfter',
                                                'tooltip' => 'tr_front_plugin_search_pagination_nbPageBeforeAfter tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'nbPageBeforeAfter',
                                                'class' => 'form-control',
                                                'placeholder' => 'tr_front_plugin_search_pagination_nbPageBeforeAfter',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                ),
                                'input_filter' => array(
                                    'current' => array(
                                        'name'     => 'current',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name'    => 'Digits',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Laminas\Validator\Digits::STRING_EMPTY => '',
                                                    ),
                                                ),
                                            ),
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                    'nbPerPage' => array(
                                        'name'     => 'nbPerPage',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name'    => 'Digits',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Laminas\Validator\Digits::STRING_EMPTY => '',
                                                    ),
                                                ),
                                            ),
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                    'nbPageBeforeAfter' => array(
                                        'name'     => 'nbPageBeforeAfter',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name'    => 'Digits',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Laminas\Validator\Digits::STRING_EMPTY => '',
                                                    ),
                                                ),
                                            ),
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                )
                            )
                        ),
                    ),
                ),
            ),
        ),
    ),
);