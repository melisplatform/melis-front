<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontTagHtmlPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/tag'),
                        'id' => 'tag01',
                        'pageId' => 1,
                        'type' => 'html',
                        'default' => 'Add HTML content here',
                        'value' => '',

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
                            'id' => 'TAGS',
                            'title' => 'tr_MelisFrontSubcategoryTag_Title'
                        ),
                        'name' => 'tr_MelisFrontTagHtmlPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontTagHtmlPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontTagHtmlPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                                'js_melistag' => '/MelisFront/plugins/js/plugin.melistagHTML.init.js'
                            ),
                        ),
                        'js_initialization' => array(),
                    ),
                ),
                'MelisFrontTagTextareaPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/tag'),
                        'id' => 'tag01',
                        'pageId' => 1,
                        'type' => 'textarea',
                        'default' => 'Add text content here',
                        'value' => '',
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
                            'id' => 'TAGS',
                            'title' => 'tr_MelisFrontSubcategoryTag_Title'
                        ),
                        'name' => 'tr_MelisFrontTagTextareaPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontTagTextareaPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontTagTextareaPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                                'js_melistag' => '/MelisFront/plugins/js/plugin.melistagTEXTAREA.init.js'
                            ),
                        ),
                        'js_initialization' => array(),
                    ),
                ),
                'MelisFrontTagMediaPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/tag'),
                        'id' => 'tag01',
                        'pageId' => 1,
                        'type' => 'media',
                        'default' => 'Add Media content here',
                        'value' => '',
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
                            'id' => 'TAGS',
                            'title' => 'tr_MelisFrontSubcategoryTag_Title'
                        ),
                        'name' => 'tr_MelisFrontTagMediaPlugin_Name',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontTagMediaPlugin_thumb.jpg',
                        'description' => 'tr_MelisFrontTagMediaPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                                'js_melistag' => '/MelisFront/plugins/js/plugin.melistagMEDIA.init.js'
                            ),
                        ),
                        'js_initialization' => array(),
                    ),
                ),
             ),
        ),
     ),
);