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
                'html-button-icon' => '<button class="column-icon half-col-equal" title="Two columns">
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
                'html-button-icon' => '<button class="column-icon three-col-equal" title="Three columns">
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
                'html-button-icon' => '<button class="column-icon four-col-equal" title="Four columns">
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
                'html-button-icon' => '<button class="column-icon five-col-equal" title="Five columns">
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
                'html-button-icon' => '<button class="column-icon six-col-equal" title="Six columns">
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
                'html-button-icon' => '<button class="column-icon cols-40-60" title="2 columns 40-60">
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
                'html-button-icon' => '<button class="column-icon cols-60-40" title="2 columns 60-40">
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
                'html-button-icon' => '<button class="column-icon cols-30-70" title="2 columns 30-70">
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
                'html-button-icon' => '<button class="column-icon cols-70-30" title="2 columns 70-30">
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
                'html-button-icon' => '<button class="column-icon cols-20-80" title="2 columns 20-80">
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
                'html-button-icon' => '<button class="column-icon cols-80-20" title="2 columns 80-20">
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
                'html-button-icon' => '<button class="column-icon cols3-3-6-3" title="3 columns 25-50-25">
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
                'html-button-icon' => '<button class="column-icon cols3-3-3-6" title="3 columns 25-25-50">
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
                'html-button-icon' => '<button class="column-icon cols3-6-3-3" title="3 columns 50-25-25">
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
                'html-button-icon' => '<button class="column-icon cols3-2-8-2" title="3 columns 16-66-16">
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
                'html-button-icon' => '<button class="column-icon cols3-2-2-8" title="3 columns 16-16-66">
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
                'html-button-icon' => '<button class="column-icon cols3-8-2-2" title="3 columns 66-16-16">
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
                'html-button-icon' => '<button class="column-icon cols4-2-2-2-6" title="4 columns 16-16-16-50">
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
                'html-button-icon' => '<button class="column-icon cols4-6-2-2-2" title="4 columns 16-16-16-50">
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
                'html-button-icon' => '<button class="column-icon half-1-col-left-w-right-2-col" title="3 columns, 1 column left with 2 columns right">
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
                'html-button-icon' => '<button class="column-icon left-2-col-w-right-1-col" title="3 columns, 2 columns left with 1 column right">
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
                'html-button-icon' => '<button class="column-icon half-1-col-left-w-right-3-col" title="4 columns, 1 column left with 3 columns right">
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
                'html-button-icon' => '<button class="column-icon left-3-col-w-half-1-col-right" title="4 columns, 3 columns left with 1 column right">
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
                'html-button-icon' => '<button class="column-icon top-1-3-col-center" title="top row 1/3 column centered, bottom row one column">
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
                'html-button-icon' => '<button class="column-icon top-1-col-w-bottom-1-3-center" title="top row one column, bottom row 1/3 column centered">
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
                'html-button-icon' => '<button class="column-icon top-1-col-w-center-1-3-center-w-bottom-1-col" title="top and bottom row one column, center row 1/3 column centered">
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
