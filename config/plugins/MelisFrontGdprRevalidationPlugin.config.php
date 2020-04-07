<?php

return [
    'plugins' => [
        'melisfront' => [
            'plugins' => [
                'MelisFrontGdprRevalidationPlugin' => [
                    'front' => [
                        'template_path' => ['MelisFront/gdpr-revalidation'],
                        'id' => 'MelisFrontGdprRevalidation',
                        'site_id' => null,
                        'module' => null,
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => [
                            'css' => [
                                '/MelisFront/plugins/css/plugin.gdprRevalidation.css',
                            ],
//                            'js' => [
//                                '/MelisFront/plugins/js/plugin.melisGdprBanner.init.js',
//                            ],
                        ],
                        'forms' => [
                            'gdpr_revalidation_form' => [
                                'attributes' => [
                                    'name' => 'gdpr_revalidation_form',
                                    'id' => 'gdpr_revalidation_form',
                                    'method' => 'POST',
                                    'action' => '',
                                ],
                                'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'name' => 'revalidate_account',
                                            'type' => 'checkbox',
                                            'options' => [
                                                'label' => 'I want to revalidate my account'
                                            ],
                                            'attributes' => [
                                                'class' => 'form-control text-center',
                                                'style' => "cursor:pointer;width:50px;height:50px;margin:0 auto;"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'melis' => [
                        'subcategory' => [
                            'id' => 'BASICS',
                            'title' => 'tr_MelisFrontSubcategoryPageBasics_Title'
                        ],
                        'name' => 'Revalidation plugin',
//                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontGdprBannerPlugin_thumb.jpg',
                        'description' => 'Revalidation plugin',
//                        'files' => [
                            'css' => [
                                '/MelisFront/plugins/css/plugin.gdprRevalidation.css',
                            ],
//                            'js' => [
//                                '/MelisFront/plugins/js/plugin.melisGdprBanner.init.js',
//                            ],
//                        ],
                        'js_initialization' => [],
                        'modal_form' => [
                            'melis_cms_gdpr_revalidation_plugin_settings_form' => [
                                'tab_title' => 'Properties',
                                'tab_icon' => 'fa fa-cog',
                                'tab_form_layout' => 'MelisFront/modal-template-form',
                                'attributes' => [
                                    'name' => 'melis_cms_gdpr_revalidation_plugin_settings_form',
                                    'id' => 'id_melis_cms_gdpr_revalidation_plugin_settings_form',
                                    'method' => 'POST',
                                    'action' => '',
                                ],
                                'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'name' => 'template_path',
                                            'type' => 'MelisEnginePluginTemplateSelect',
                                            'options' => [
                                                'label' => 'tr_melis_Plugins_Template',
                                                'tooltip' => 'tr_melis_Plugins_Template tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ],
                                            'attributes' => [
                                                'id' => 'id_page_tpl_id',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ],
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'name' => 'site_id',
                                            'type' => 'MelisCmsPluginSiteSelect',
                                            'options' => [
                                                'label' => 'tr_melis_engine_sites',
                                                'tooltip' => 'tr_melis_engine_sites_select',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ],
                                            'attributes' => [
                                                'id' => 'id_page_tpl_id',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ],
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'name' => 'module',
                                            'type' => 'MelisCoreGdprModuleSelect',
                                            'options' => [
                                                'label' => 'Associated Module',
                                                'tooltip' => 'The module associated to this plugin',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ],
                                            'attributes' => [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ],
                                        ],
                                    ],
                                ],
                                'input_filter' => [
                                    'template_path' => [
                                        'name' => 'template_path',
                                        'required' => true,
                                        'validators' => [
                                            [
                                                'name' => 'NotEmpty',
                                                'options' => [
                                                    'messages' => [
                                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'filters' => [],
                                    ],
                                    'site_id' => [
                                        'name' => 'site_id',
                                        'required' => true,
//                                        'validators' => [
//                                            [
//                                                'name' => 'NotEmpty',
//                                                'options' => [
//                                                    'messages' => [
//                                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
//                                                    ],
//                                                ],
//                                            ],
//                                        ],
//                                        'filters' => [],
                                    ],
                                    'module_associated' => [
                                        'name' => 'module',
                                        'required' => true,
//                                        'validators' => [
//                                            [
//                                                'name' => 'NotEmpty',
//                                                'options' => [
//                                                    'messages' => [
//                                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
//                                                    ],
//                                                ],
//                                            ],
//                                        ],
//                                        'filters' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
