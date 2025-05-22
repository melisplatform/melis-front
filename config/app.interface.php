<?php
return [
    'plugins' => [
        'melisfront' => [
            'datas' => [
                'default' => [
                    'errors' => [
                        'error_reporting' => E_ALL & ~E_USER_DEPRECATED,
                        'display_errors' => 1,
                    ],
                ],
                'gdpr_auto_anonymized_time_format' => 'd'
            ],
            'resources' => [
                'js' => [],
                'css' => [],
            ]
        ],
        'drag-and-drop-layouts' => [
            'default' => [
                'template' => 'MelisFront/dnd-default-tpl',
                'html-button-icon' => '<button class="column-icon whole-1-col">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>'
            ],
            '2-cols' => [
                'template' => 'MelisFront/dnd-2-cols-tpl',
                'html-button-icon' => '<button class="column-icon half-col-equal">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-rows-1-3-col-center-w-bottom-1-col' => [
                'template' => 'MelisFront/dnd-2-rows-1-3-col-center-w-bottom-1-col-tpl',
                'html-button-icon' => '<button class="column-icon top-1-3-col-center">
                                        <div class="icon-container">
                                            <div class="icon-row justify-content-center">
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                            </div>
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-rows-top1-col-w-bottom-1-3-col-center' => [
                'template' => 'MelisFront/dnd-2-rows-top1-col-w-bottom-1-3-col-center-tpl',
                'html-button-icon' => '<button class="column-icon top-1-col-w-bottom-1-3-center">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                            <div class="icon-row justify-content-center">
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ]
        ]
    ]
];
