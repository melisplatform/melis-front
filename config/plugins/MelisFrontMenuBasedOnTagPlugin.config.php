<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontMenuBasedOnTagPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/menu-based-on-tag'),
                        'id' => 'tagBasedMenu',
                        'tagToUse' => '',
                        'menuTitle' => '',
                        
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                                '/MelisFront/plugins/js/plugin.menuBasedOnTag.js'
                            ),
                        ),
                    ),
                    'melis' => array(
                        'subcategory' => array(
                            'id' => 'BASICS',
                            'title' => 'tr_MelisFrontSubcategoryPageBasics_Title'
                        ),
                        'name' => 'tr_MelisFrontMenuBasedOnTagPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontMenuBasedOnTagPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontMenuBasedOnTagPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                            ),
                        ),
                        'js_initialization' => array(),
                        'tags_list' => [
                            'h1', 'h2', 'h3',
                            'h4', 'h5'
                        ],
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
                            'plugin_menu_editor_tab_1' => array(
                                'tab_title' => 'tr_front_plugin_tab_properties',
                                'tab_icon'  => 'fa fa-cog',
                                'tab_form_layout' => 'MelisFront/menu-based-on-tag-form',
                                'attributes' => array(
                                    'name' => 'menu_editor_tab_1',
                                    'id' => 'menu_editor_tab_1',
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
                                            'name' => 'tagToUse',
                                            'type' => 'MelisEngineMenuBasedOnTagTagSelect',
                                            'options' => array(
                                                'label' => 'tr_melis_Plugins_tag_to_use',
                                                'tooltip' => 'tr_melis_Plugins_tag_to_use tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ),
                                            'attributes' => array(
                                                'id' => 'id_page_tpl_id',
                                                'class' => 'form-control',
//                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'menuTitle',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_menu_title',
                                                'tooltip' => 'tr_front_plugin_menu_title tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'menuTitle', 
                                                'required' => 'required',
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
                                    'tagToUse' => array(
                                        'name'     => 'tagToUse',
                                        'required' => false,
//                                        'validators' => array(
//                                            array(
//                                                'name' => 'NotEmpty',
//                                                'options' => array(
//                                                    'messages' => array(
//                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_tagToUse_empty',
//                                                    ),
//                                                ),
//                                            ),
//                                        ),
//                                        'filters'  => array(
//                                        ),
                                    ),
                                    'menuTitle' => array(
                                        'name'     => 'menuTitle',
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
                                        ),
                                    ),
                                )
                            ),
                        )
                    ),
                ),
             ),
        ),
     ),
);