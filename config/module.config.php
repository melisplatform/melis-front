<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

return [
    'router' => [
        'routes' => [
            'melis-front' => [
                'type'    => 'Regex',
                'options' => [
                    'regex' => '.*/id/(?<idpage>[0-9]+)',
                    'defaults' => [
                        'controller' => 'MelisFront\Controller\Index',
                        'action' => 'index',
                        'renderType' => 'melis_zf2_mvc',
                        'renderMode' => 'front',
                        'preview' => false,
                    ],
                    'spec' => '%idpage'
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'melis_front_melisrender' => [
                        'type'    => 'Regex',
                        'options' => [
                            'regex'    => '/renderMode/melis',
                            'defaults' => [
                                'renderMode' => 'melis',
                                'preview' => false,
                            ],
                            'spec' => ''
                        ],
                    ],
                    'melis_front_previewender' => [
                        'type'    => 'Regex',
                        'options' => [
                            'regex'    => '/preview',
                            'defaults' => [
                                'renderMode' => 'melis',
                                'preview' => true,
                            ],
                            'spec' => ''
                        ],
                    ],
                ],
            ],
            'melis-front-special-urls' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'MelisFront\Controller\SpecialUrls',
                    ]
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'front-sitemap' => [
                        'type'    => 'Regex',
                        'options' => [
                            'regex' => 'sitemap.html|sitemap.xml|sitemap',
                            'defaults' => [
                                'action' => 'sitemap',
                            ],
                            'spec' => ''
                        ],
                    ],
                    'front-plugin-widths' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => 'css/plugin-width.css',
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisFront\Controller',
                                'controller' => 'Style',
                                'action' => 'pluginWidths',
                            ],
                        ],
                    ],
                    'front-page-plugin-widths' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => 'css/page-plugin-width.css',
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisFront\Controller',
                                'controller' => 'Style',
                                'action' => 'getPagePluginWidthCss',
                            ],
                        ],
                    ],
                    'front-module-ctrl-action' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'MelisFront/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisFront\Controller',
                            ],
                        ],
                    ],
                    'front-search-indexer' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'melissearchindex/module[/:moduleName]/pageid[/:pageid]/exclude-pageid[/:expageid]',
                            'constraints' => [
                                'moduleName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'pageid'     => '[0-9]+',
                                'expageid'   => '[0-9;]+',
                            ],
                            'defaults' => [
                                'controller' => 'MelisFront\Controller\MelisFrontSearch',
                                'action' => 'addLuceneIndex',
                            ],
                        ],
                    ],
                    'front-search-indexer-optimize' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'melisoptimizeindex/module[/:moduleName]',
                            'constraints' => [
                                'moduleName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'MelisFront\Controller\MelisFrontSearch',
                                'action' => 'optimizeIndex',
                            ],
                        ],
                    ],

                ],
            ],
            'melis-plugin-renderer' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/melispluginrenderer',
                    'defaults' => [
                        'controller' => 'MelisFront\Controller\MelisPluginRenderer',
                        'action'     => 'getPlugin',
                        'renderMode'     => 'melis',
                        'preview'        => false,
                    ],
                ],
            ],
            'sites-minify-assets' => [
                'type' => 'Segment',
                'options' => [
                    'route' =>  '/minify-assets',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'MelisFront\Controller\MinifyAssets',
                        'action' => 'minifyAssets',
                    ],
                ],
            ],
            'dnd-layout' => [
                'type' => 'Literal',
                'options' => [
                    'route' =>  '/dnd-layout',
                    'constraints' => [
                        'action' => '',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'MelisFront\Controller',
                        'controller' => 'MelisPluginRenderer',
                        'action' => 'dndLayout',
                        'renderMode' => 'melis',
                    ],
                ],
            ],
            'dnd-remove' => [
                'type' => 'Literal',
                'options' => [
                    'route' =>  '/dnd-remove',
                    'constraints' => [
                        'action' => '',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'MelisFront\Controller',
                        'controller' => 'MelisPluginRenderer',
                        'action' => 'dndRemove',
                        'renderMode' => 'melis',
                    ],
                ],
            ],
            'dnd-update-order' => [
                'type' => 'Literal',
                'options' => [
                    'route' =>  '/dnd-update-order',
                    'constraints' => [
                        'action' => '',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'MelisFront\Controller',
                        'controller' => 'MelisPluginRenderer',
                        'action' => 'dndUpdateOrder',
                        'renderMode' => 'melis',
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'locale' => 'en_EN',
    ],
    'service_manager' => [
        'factories' => [
            // Navigation
            'MelisFrontNavigation'  => \MelisFront\Navigation\Factory\MelisFrontNavigationFactory::class,
        ],
        'aliases' => [
            // Laminas Mvc translator Service
            'translator'                    => 'MvcTranslator',
            'MelisFrontHead'                => \MelisFront\Service\MelisFrontHeadService::class,
            'MinifyAssets'                  => \MelisFront\Service\MinifyAssetsService::class,
            'MelisSiteTranslationService'   => \MelisFront\Service\MelisSiteTranslationService::class,
            'MelisSiteConfigService'        => \MelisFront\Service\MelisSiteConfigService::class,
            'MelisTranslationService'       => \MelisFront\Service\MelisTranslationService::class,
        ],
        'abstract_factories' => [
            /**
             * This Abstract factory will create requested service
             * that match on the onCreate() conditions
             */
            \MelisCore\Factory\MelisAbstractFactory::class
        ]
    ],
    'controllers' => [
        'invokables' => [
            'MelisFront\Controller\Index'               => \MelisFront\Controller\IndexController::class,
            'MelisFront\Controller\SpecialUrls'         => \MelisFront\Controller\SpecialUrlsController::class,
            'MelisFront\Controller\MelisFrontSearch'    => \MelisFront\Controller\MelisFrontSearchController::class,
            'MelisFront\Controller\MelisPluginRenderer' => \MelisFront\Controller\MelisPluginRendererController::class,
            'MelisFront\Controller\Style'               => \MelisFront\Controller\StyleController::class,
            'MelisFront\Controller\MinifyAssets'        => \MelisFront\Controller\MinifyAssetsController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'MelisFrontDragDropZonePlugin'          => \MelisFront\Controller\Plugin\MelisFrontDragDropZonePlugin::class,
            'MelisFrontTagHtmlPlugin'               => \MelisFront\Controller\Plugin\MelisFrontTagHtmlPlugin::class,
            'MelisFrontTagTextareaPlugin'           => \MelisFront\Controller\Plugin\MelisFrontTagTextareaPlugin::class,
            'MelisFrontTagMediaPlugin'              => \MelisFront\Controller\Plugin\MelisFrontTagMediaPlugin::class,
            'MelisFrontMenuPlugin'                  => \MelisFront\Controller\Plugin\MelisFrontMenuPlugin::class,
            'MelisFrontBreadcrumbPlugin'            => \MelisFront\Controller\Plugin\MelisFrontBreadcrumbPlugin::class,
            'MelisFrontShowListFromFolderPlugin'    => \MelisFront\Controller\Plugin\MelisFrontShowListFromFolderPlugin::class,
            /**
             * @TODO ZendSearch equivalent for Laminas
             */
            //            'MelisFrontSearchResultsPlugin'         => \MelisFront\Controller\Plugin\MelisFrontSearchResultsPlugin::class,
            'MelisFrontBlockSectionPlugin'          => \MelisFront\Controller\Plugin\MelisFrontBlockSectionPlugin::class,
            'MiniTemplatePlugin'                    => \MelisFront\Controller\Plugin\MiniTemplatePlugin::class,
            'MelisFrontGdprBannerPlugin'            => \MelisFront\Controller\Plugin\MelisFrontGdprBannerPlugin::class,
            'MelisFrontGdprRevalidationPlugin'      => \MelisFront\Controller\Plugin\MelisFrontGdprRevalidationPlugin::class,
            'MelisFrontGenericContentPlugin'        => \MelisFront\Controller\Plugin\MelisFrontGenericContentPlugin::class,
        ]
    ],
    'view_helpers' => [
        'aliases' => [
            'MelisDragDropZone'         => \MelisFront\View\Helper\MelisDragDropZoneHelper::class,
            'MelisTag'                  => \MelisFront\View\Helper\MelisTagsHelper::class,
            'MelisMenu'                 => \MelisFront\View\Helper\MelisMenuHelper::class,
            'MelisLink'                 => \MelisFront\View\Helper\MelisLinksHelper::class,
            'MelisPageLangLink'         => \MelisFront\View\Helper\MelisPageLangVersionLinkHelper::class,
            'MelisHomePageLink'         => \MelisFront\View\Helper\MelisHomePageLinkHelper::class,
            'siteTranslate'             => \MelisFront\View\Helper\MelisSiteTranslationHelper::class,
            'boTranslate'               => \MelisFront\View\Helper\MelisTranslationHelper::class,
            'SiteConfig'                => \MelisFront\View\Helper\SiteConfigViewHelper::class,
            'MelisGdprBannerPlugin'     => \MelisFront\View\Helper\MelisGdprBannerHelper::class,
            'MelisListFromFolderPlugin' => \MelisFront\View\Helper\MelisListFromFolderHelper::class,
        ],
        'abstract_factories' => [
            /**
             * This Abstract factory will create requested service
             * that match on the onCreate() conditions
             */
            \MelisCore\Factory\MelisAbstractFactory::class
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'layout/layout'                                 => __DIR__ . '/../view/layout/layoutBlank.phtml',
            'layout/layoutFront'                            => __DIR__ . '/../view/layout/layoutFront.phtml',
            'layout/layoutMelis'                            => __DIR__ . '/../view/layout/layoutMelis.phtml',
            'melis-front/index/index'                       => __DIR__ . '/../view/melis-front/index/index.phtml',
            'MelisFront/dragdropzone'                       => __DIR__ . '/../view/melis-front/plugins/dragdropzone.phtml',
            'MelisFront/dragdropzone/meliscontainer'        => __DIR__ . '/../view/melis-front/plugins/dragdropzone-melis-container.phtml',
            'MelisFront/dragdropzone/meliscontainer-buttons' => __DIR__ . '/../view/melis-front/plugins/dragdropzone-melis-container-buttons.phtml',
            'MelisFront/tag'                                => __DIR__ . '/../view/melis-front/plugins/tag.phtml',
            'MelisFront/tag/meliscontainer'                 => __DIR__ . '/../view/melis-front/plugins/tag-melis-container.phtml',
            'MelisFront/menu'                               => __DIR__ . '/../view/melis-front/plugins/menu.phtml',
            'MelisFront/menu/melis/form'                    => __DIR__ . '/../view/melis-front/plugins/menu-melis-form.phtml',
            'MelisFront/breadcrumb'                         => __DIR__ . '/../view/melis-front/plugins/breadcrumb.phtml',
            'MelisFront/breadcrumb/melis/form'              => __DIR__ . '/../view/melis-front/plugins/breadcrumb-melis-form.phtml',
            'MelisFront/show-list-from-folder'              => __DIR__ . '/../view/melis-front/plugins/show-list-from-folder.phtml',
            'MelisFront/show-list/melis/form'               => __DIR__ . '/../view/melis-front/plugins/show-list-from-fold-form.phtml',
            'MelisFront/search-results'                     => __DIR__ . '/../view/melis-front/plugins/search-results.phtml',
            'MelisFront/search/melis/form'                  => __DIR__ . '/../view/melis-front/plugins/search-melis-template-form.phtml',
            'MelisFront/list-paginator'                     => __DIR__ . '/../view/melis-front/plugins/list-paginator.phtml',
            'MelisFront/block-section'                      => __DIR__ . '/../view/melis-front/plugins/block-section.phtml',
            'MelisFront/block-section-container'            => __DIR__ . '/../view/melis-front/plugins/block-section-container.phtml',
            'MelisFront/gdpr-banner'                        => __DIR__ . '/../view/melis-front/plugins/gdpr-banner.phtml',
            'MelisFront/modal-template-form'                => __DIR__ . '/../view/melis-front/plugins/modal-template-form.phtml',
            'MelisFront/gdpr-revalidation'                  => __DIR__ . '/../view/melis-front/plugins/gdpr-revalidation.phtml',
            //Mini Template plugins
            'MiniTemplate/Content'                          =>  __DIR__ . '/../view/melis-front/plugins/mini-template-default.phtml',
            'MelisFront/generic-content'                    => __DIR__ . '/../view/melis-front/plugins/generic-content.phtml',
            // Drag in drop layouts
            'MelisFront/dnd'                                                        => __DIR__ . '/../view/melis-front/plugins/dnd.phtml',
            'MelisFront/dnd-default-tpl'                                            => __DIR__ . '/../view/melis-front/plugins/dnd-default-tpl.phtml',
            'MelisFront/dnd-2-cols-tpl'                                             => __DIR__ . '/../view/melis-front/plugins/dnd-2-cols-tpl.phtml',
            'MelisFront/dnd-3-cols-tpl'                                             => __DIR__ . '/../view/melis-front/plugins/dnd-3-cols-tpl.phtml',
            'MelisFront/dnd-4-cols-tpl'                                             => __DIR__ . '/../view/melis-front/plugins/dnd-4-cols-tpl.phtml',
            'MelisFront/dnd-3-cols-1-col-left-w-2-cols-right-tpl'                   => __DIR__ . '/../view/melis-front/plugins/dnd-3-cols-1-col-left-w-2-cols-right-tpl.phtml',
            'MelisFront/dnd-3-cols-2-cols-left-w-1-col-right-tpl'                   => __DIR__ . '/../view/melis-front/plugins/dnd-3-cols-2-cols-left-w-1-col-right-tpl.phtml',
            'MelisFront/dnd-4-cols-1-col-left-w-3-cols-right-tpl'                   => __DIR__ . '/../view/melis-front/plugins/dnd-4-cols-1-col-left-w-3-cols-right-tpl.phtml',
            'MelisFront/dnd-4-cols-3-cols-left-w-1-col-right-tpl'                   => __DIR__ . '/../view/melis-front/plugins/dnd-4-cols-3-cols-left-w-1-col-right-tpl.phtml',
            'MelisFront/dnd-2-cols-down-tpl'                                        => __DIR__ . '/../view/melis-front/plugins/dnd-2-cols-down-tpl.phtml',
            'MelisFront/dnd-2-rows-1-3-col-center-w-bottom-1-col-tpl'               => __DIR__ . '/../view/melis-front/plugins/dnd-2-rows-1-3-col-center-w-bottom-1-col-tpl.phtml',
            'MelisFront/dnd-2-rows-top-1-col-w-bottom-1-3-col-center-tpl'           => __DIR__ . '/../view/melis-front/plugins/dnd-2-rows-top-1-col-w-bottom-1-3-col-center-tpl.phtml',
            'MelisFront/dnd-3-rows-top-1-col-w-1-3-col-center-w-bottom-1-col-tpl'   => __DIR__ . '/../view/melis-front/plugins/dnd-3-rows-top-1-col-w-1-3-col-center-w-bottom-1-col-tpl.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'aliases' => [
                'MelisFront/' => __DIR__ . '/../public/',
            ],
        ],
    ],
    'caches' => [
        'melisfront_pages_file_cache' => [
            'active' => true, // activate or deactivate Melis Cache for this conf
            'adapter' => 'Filesystem',
            'options' => [
                'ttl' => 0, // 24hrs
                'namespace' => 'melisfront_pages_file_cache',
                'cache_dir' => $_SERVER['DOCUMENT_ROOT'] . '/../cache'
            ],
            'plugins' => [
                [
                    'name' => 'exception_handler',
                    'options' => [
                        'throw_exceptions' => false
                    ],
                ],
                [
                    'name' => 'Serializer'
                ]
            ],
            'ttls' => [
                // add a specific ttl for a specific cache key (found via regexp)
                // 'my_cache_key' => 60,
            ]
        ],
        'melisfront_memory_cache' => [
            'active' => true, // activate or deactivate Melis Cache for this conf
            'adapter' => 'Memory',
            'options' => ['ttl' => 0, 'namespace' => 'melisfront'],
            'plugins' => [
                [
                    'name' => 'exception_handler',
                    'options' => [
                        'throw_exceptions' => false
                    ],
                ]
            ],
            'ttls' => [
                // add a specific ttl for a specific cache key
                // 'my_cache_key' => 60,
            ]
        ],
    ]
];
