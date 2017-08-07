<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontShowListFromFolderPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/show-list-from-folder'),
                        'id' => 'show-list-from-folder',
                        'pageId' => 1,
                        'pageIdFolder' => 1,
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
                        'name' => 'tr_MelisFrontShowListFromFolderPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontShowListFromFolderPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontShowListFromFolderPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                            ),
                        ),
                        'js_initialization' => array(),
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
                                            'name' => 'pageIdFolder',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_front_plugin_showlistfromfolder_root_page',
                                                'tooltip' => 'tr_front_plugin_showlistfromfolder_root_page tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'pageIdFolder',
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

                                    'pageIdFolder' => array(
                                        'name'     => 'pageIdFolder',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melis_plugins_page_id_empty',
                                                    ),
                                                ),
                                            ),
                                            array(
                                                'name' => 'IsInt',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Zend\I18n\Validator\IsInt::NOT_INT => 'tr_melis_plugins_page_id_not_num'
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