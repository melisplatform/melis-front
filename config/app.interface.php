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
            '3-cols' => [
                'template' => 'MelisFront/dnd-3-cols-tpl',
                'html-button-icon' => '<button class="column-icon three-col-equal">
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
                'html-button-icon' => '<button class="column-icon four-col-equal">
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
            '5-cols' => [
                'template' => 'MelisFront/dnd-5-cols-tpl',
                'html-button-icon' => '<button class="column-icon five-col-equal">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col bg-white"></div>
                                                <div class="icon-col-bg icon-col bg-white"></div>
                                                <div class="icon-col-bg icon-col bg-white"></div>
                                                <div class="icon-col-bg icon-col bg-white"></div>
                                                <div class="icon-col-bg icon-col bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '6-cols' => [
                'template' => 'MelisFront/dnd-6-cols-tpl',
                'html-button-icon' => '<button class="column-icon six-col-equal">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-cols-40-60' => [
                'template' => 'MelisFront/dnd-2-cols-40-60-tpl',
                'html-button-icon' => '<button class="column-icon cols-40-60">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-5 bg-white"></div>
                                                <div class="icon-col-bg icon-col-7 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-cols-60-40' => [
                'template' => 'MelisFront/dnd-2-cols-60-40-tpl',
                'html-button-icon' => '<button class="column-icon cols-60-40">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-7 bg-white"></div>
                                                <div class="icon-col-bg icon-col-5 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-cols-30-70' => [
                'template' => 'MelisFront/dnd-2-cols-30-70-tpl',
                'html-button-icon' => '<button class="column-icon cols-30-70">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                                <div class="icon-col-bg icon-col-8 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-cols-70-30' => [
                'template' => 'MelisFront/dnd-2-cols-70-30-tpl',
                'html-button-icon' => '<button class="column-icon cols-70-30">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-8 bg-white"></div>
                                                <div class="icon-col-bg icon-col-4 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-cols-20-80' => [
                'template' => 'MelisFront/dnd-2-cols-20-80-tpl',
                'html-button-icon' => '<button class="column-icon cols-20-80">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-10 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '2-cols-80-20' => [
                'template' => 'MelisFront/dnd-2-cols-80-20-tpl',
                'html-button-icon' => '<button class="column-icon cols-80-20">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-10 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-3-6-3' => [
                'template' => 'MelisFront/dnd-3-cols-3-6-3-tpl',
                'html-button-icon' => '<button class="column-icon cols3-3-6-3">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-3-3-6' => [
                'template' => 'MelisFront/dnd-3-cols-3-3-6-tpl',
                'html-button-icon' => '<button class="column-icon cols3-3-3-6">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-6-3-3' => [
                'template' => 'MelisFront/dnd-3-cols-6-3-3-tpl',
                'html-button-icon' => '<button class="column-icon cols3-6-3-3">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                                <div class="icon-col-bg icon-col-3 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-2-8-2' => [
                'template' => 'MelisFront/dnd-3-cols-2-8-2-tpl',
                'html-button-icon' => '<button class="column-icon cols3-2-8-2">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-8 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-2-2-8' => [
                'template' => 'MelisFront/dnd-3-cols-2-2-8-tpl',
                'html-button-icon' => '<button class="column-icon cols3-2-2-8">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-8 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '3-cols-8-2-2' => [
                'template' => 'MelisFront/dnd-3-cols-8-2-2-tpl',
                'html-button-icon' => '<button class="column-icon cols3-8-2-2">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-8 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '4-cols-2-2-2-6' => [
                'template' => 'MelisFront/dnd-4-cols-2-2-2-6-tpl',
                'html-button-icon' => '<button class="column-icon cols4-2-2-2-6">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                            </div>
                                        </div>
                                    </button>',
            ],
            '4-cols-6-2-2-2' => [
                'template' => 'MelisFront/dnd-4-cols-6-2-2-2-tpl',
                'html-button-icon' => '<button class="column-icon cols4-6-2-2-2">
                                        <div class="icon-container">
                                            <div class="icon-row">
                                                <div class="icon-col-bg icon-col-6 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
                                                <div class="icon-col-bg icon-col-2 bg-white"></div>
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
            '2-rows-top-1-col-w-bottom-1-3-col-center' => [
                'template' => 'MelisFront/dnd-2-rows-top-1-col-w-bottom-1-3-col-center-tpl',
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
            ],
            '3-rows-top-1-col-w-1-3-col-center-w-bottom-1-col' => [
                'template' => 'MelisFront/dnd-3-rows-top-1-col-w-1-3-col-center-w-bottom-1-col-tpl',
                'html-button-icon' => '<button class="column-icon top-1-col-w-center-1-3-center-w-bottom-1-col">
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
