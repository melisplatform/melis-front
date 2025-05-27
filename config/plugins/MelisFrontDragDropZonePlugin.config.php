<?php

return [
    'plugins' => [
        'melisfront' => [
            'conf' => [
                // user rights exclusions
                'rightsDisplay' => 'none',
            ],
            'plugins' => [
                'MelisFrontDragDropZonePlugin' => [
                    'front' => [
                        'template_path' => ['MelisFront/dragdropzone'],
                        'id' => 'dragdropzone',
                        'pageId' => 1,
                        'plugins' => [],
                        // d&d indicator for rendering multiple d&d zone
                        // default is false as the outer of d&d to render first before inner d&ds
                        // d&d layout must be pass 3 param "true" to render as inner d&d zone
                        'isInnerDragDropZone' => false,
                        // plugin referer is the based dnd of the related dnd
                        'plugin_referer' => '',

                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => [
                            'css' => [],
                            'js' => [],
                        ],
                    ],
                    'melis' => [
                        'files' => [
                            'css' => [
                                'css_melisdragdropzone' => '/MelisFront/plugins/css/plugin.melisdragdropzone.css',
                                'css_resize_plugins_class_bo' => '/MelisFront/plugins/css/plugin-width.min.css',
                            ],
                            'js' => [
                                'js_melisdragdropzone' => '/MelisFront/plugins/js/plugin.melisdragdropzone.js'
                            ],
                        ],
                        'js_initialization' => [],
                    ],
                ],
            ],
        ],
    ],
];
