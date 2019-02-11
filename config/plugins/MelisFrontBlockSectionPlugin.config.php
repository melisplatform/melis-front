<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontBlockSectionPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/block-section'),
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                    ),
                    'melis' => array(
                        'subcategory' => array(
                            'id' => 'BASICS',
                            'title' => 'tr_MelisFrontSubcategoryPageBasics_Title'
                        ),
                        'name' => 'Block',
                        'thumbnail' => '/MelisFront/plugins/images/MelisFrontBlockPlugin_thumb.jpg',
                        'description' => 'tr_melis_front_bloc_plugin_description',

                    ),
                ),
            ),
        ),
    ),
);