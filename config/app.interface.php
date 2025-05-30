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
                'html-button-icon' => '<button class="column-icon whole-1-col" title="One column">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>'
            ],
            '2-cols' => [
                'template' => 'MelisFront/dnd-2-cols-tpl',
                'html-button-icon' => '<button class="column-icon half-col-equal" title="Two column">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols' => [
                'template' => 'MelisFront/dnd-3-cols-tpl',
                'html-button-icon' => '<button class="column-icon three-col-equal" title="Three column">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '4-cols' => [
                'template' => 'MelisFront/dnd-4-cols-tpl',
                'html-button-icon' => '<button class="column-icon four-col-equal" title="Four column">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-1-col-left-w-2-cols-right' => [
                'template' => 'MelisFront/dnd-3-cols-1-col-left-w-2-cols-right-tpl',
                'html-button-icon' => '<button class="column-icon half-1-col-left-w-right-2-col">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-6 icon-col-bg bg-white">
                                                </div>
                                                <div class="icon-col-6">
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                </div>	
                                            </div>
                                        </div>
                                    </button>'
            ],
            '3-cols-2-cols-left-w-1-col-right' => [
                'template' => 'MelisFront/dnd-3-cols-2-cols-left-w-1-col-right-tpl',
                'html-button-icon' => '<button class="column-icon left-2-col-w-right-1-col">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-6">
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                </div>
                                                <div class="icon-col-6 icon-col-bg bg-white">
                                                </div>	
                                            </div>
                                        </div>
                                    </button>'
            ],
            '4-cols-1-col-left-w-3-cols-right' => [
                'template' => 'MelisFront/dnd-4-cols-1-col-left-w-3-cols-right-tpl',
                'html-button-icon' => '<button class="column-icon half-1-col-left-w-right-3-col">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-6 icon-col-bg bg-white">
                                                </div>
                                                <div class="icon-col-6">
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                </div>	
                                            </div>
                                        </div>
                                    </button>'
            ],
            '4-cols-3-cols-left-w-1-col-right' => [
                'template' => 'MelisFront/dnd-4-cols-3-cols-left-w-1-col-right-tpl',
                'html-button-icon' => '<button class="column-icon left-3-col-w-half-1-col-right">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-6">
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                    <div class="icon-row">
                                                        <div class="icon-col-bg icon-col-12 bg-white"></div>
                                                    </div>
                                                </div>
                                                <div class="icon-col-6 icon-col-bg bg-white">
                                                </div>
                                            </div>
                                        </div>
                                    </button>'
            ],            
            '2-rows-1-3-col-center-w-bottom-1-col' => [
                'template' => 'MelisFront/dnd-2-rows-1-3-col-center-w-bottom-1-col-tpl',
                'html-button-icon' => '<button class="column-icon top-1-3-col-center" title="Top row 1/3 column centered, bottom row one column">
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
            '2-rows-top-1-col-w-bottom-1-3-col-center' => [
                'template' => 'MelisFront/dnd-2-rows-top-1-col-w-bottom-1-3-col-center-tpl',
                'html-button-icon' => '<button class="column-icon top-1-col-w-bottom-1-3-center" title="Top row one column, bottom row 1/3 column centered">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                            <div class="icon-row justify-content-center">
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-rows-top-1-col-w-1-3-col-center-w-bottom-1-col' => [
                'template' => 'MelisFront/dnd-3-rows-top-1-col-w-1-3-col-center-w-bottom-1-col-tpl',
                'html-button-icon' => '<button class="column-icon top-1-col-w-center-1-3-center-w-bottom-1-col" title="Top and bottom row one column, center row 1/3 column centered">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                            <div class="icon-row justify-content-center">
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                            </div>
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-12 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ]
        ]
    ]
];
