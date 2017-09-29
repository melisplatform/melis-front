<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontDragDropZonePlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisFront/dragdropzone'),
                        'id' => 'dragdropzone',
                        'pageId' => 1,
                        'plugins' => array(),
                        
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => array(
                            'css' => array(
                                'css_resize_plugins_class' => 'css/plugin-width.css',
                            ),
                            'js' => array(
                            ),
                        ),
                    ),
                    'melis' => array(
                        'files' => array(
                            'css' => array(
                                'css_melisdragdropzone' => '/MelisFront/plugins/css/plugin.melisdragdropzone.css',
                                'css_resize_plugins_class' => 'css/plugin-width.css',
                            ),
                            'js' => array(
                                'js_melisdragdropzone' => '/MelisFront/plugins/js/plugin.melisdragdropzone.js'
                            ),
                        ),
                        'js_initialization' => array(),
                    ),
                ),
             ),
        ),
     ),
);