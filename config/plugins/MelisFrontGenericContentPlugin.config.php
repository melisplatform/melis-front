<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontGenericContentPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/generic-content'),
                        'id' => 'melisfront-generic-content-plugin',
                        'pageId' => 1,
                        'parentPageId' => null,
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                                 'js_melistag' => '/MelisFront/plugins/js/plugin.melistagHTML.init.js',
                            ),
                        ),
                    ),
                    'melis' => array(
                        'section' => 'MelisCms',
                        'subcategory' => array(
                            'id' => 'BASICS',
                            'title' => 'tr_MelisFrontSubcategoryPageBasics_Title'
                        ),
                        'name' => 'tr_melis_front_generic_plugin_name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontGenerictContentPlugin_thumb.png',
                        'description' => 'tr_melis_front_generic_plugin_description',
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
                        'modal_form' => array(
                            'plugin_show_list_tab_1' => array(
                                'tab_title' => 'tr_front_plugin_tab_properties',
                                'tab_icon'  => 'fa fa-cog',
                                'tab_form_layout' => 'MelisFront/show-list/melis/form',
                                'attributes' => array(
                                    'name' => 'show_list_plugin_tab_1',
                                    'id' => 'show_list_plugin_tab_1',
                                    'method' => '',
                                    'action' => '',
                                ),
                                'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'name' => 'parentPageId',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_melis_plugins_page_id',
                                                'tooltip' => 'tr_melis_plugins_page_id tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'parentPageId',
                                                'class' => 'melis-input-group-button',
                                                'data-button-icon' => 'fa fa-sitemap',
                                                'data-button-id' => 'meliscms-site-selector',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                ),
                                'input_filter' => array(
                                    'parentPageId' => array(
                                        'name'     => 'parentPageId',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melis_plugins_page_id_empty',
                                                    ),
                                                ),
                                            ),
                                            array(
                                                'name' => 'IsInt',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\I18n\Validator\IsInt::NOT_INT => 'tr_melis_plugins_page_id_not_num'
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