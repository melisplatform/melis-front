<?php
/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

use MelisEngine\Service\Factory\AbstractFactory;
use MelisFront\View\Helper\Factory\AbstractViewHelperFactory;

return [
    'router' => [
        'routes' => [
            'melis-front' => [
                'type'    => 'regex',
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
                        'type'    => 'regex',
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
                        'type'    => 'regex',
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
                        'type'    => 'regex',
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
                        'preview'		=> false,
                    ],
                ],
            ],
            'sites-minify-assets' => [
                'type' => 'segment',
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
        ],
    ],
    'service_manager' => [
        'factories' => [
            // Services
            \MelisFront\Service\MelisFrontHeadService::class        => AbstractFactory::class,
            \MelisFront\Service\MinifyAssetsService::class          => AbstractFactory::class,
            \MelisFront\Service\MelisSiteTranslationService::class  => AbstractFactory::class,
            \MelisFront\Service\MelisSiteConfigService::class       => AbstractFactory::class,
            \MelisFront\Service\MelisTranslationService::class      => AbstractFactory::class,
            // Navigation
            'MelisFrontNavigation'  => \MelisFront\Navigation\Factory\MelisFrontNavigationFactory::class,
        ],
        'aliases' => [
            'MelisFrontHead'                => \MelisFront\Service\MelisFrontHeadService::class,
            'MinifyAssets'                  => \MelisFront\Service\MinifyAssetsService::class,
            'MelisSiteTranslationService'   => \MelisFront\Service\MelisSiteTranslationService::class,
            'MelisSiteConfigService'        => \MelisFront\Service\MelisSiteConfigService::class,
            'MelisTranslationService'       => \MelisFront\Service\MelisTranslationService::class,
        ],
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
            'MelisFrontSearchResultsPlugin'         => \MelisFront\Controller\Plugin\MelisFrontSearchResultsPlugin::class,
            'MelisFrontBlockSectionPlugin'          => \MelisFront\Controller\Plugin\MelisFrontBlockSectionPlugin::class,
            'MiniTemplatePlugin'                    => \MelisFront\Controller\Plugin\MiniTemplatePlugin::class,
            'MelisFrontGdprBannerPlugin'            => \MelisFront\Controller\Plugin\MelisFrontGdprBannerPlugin::class,
        ]
    ],
    'view_helpers' => [
        'factories' => [
            \MelisFront\View\Helper\MelisDragDropZoneHelper::class          => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisTagsHelper::class                  => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisMenuHelper::class                  => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisLinksHelper::class                 => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisPageLangVersionLinkHelper::class   => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisHomePageLinkHelper::class          => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisSiteTranslationHelper::class       => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisTranslationHelper::class           => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\SiteConfigViewHelper::class             => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisGdprBannerHelper::class            => AbstractViewHelperFactory::class,
            \MelisFront\View\Helper\MelisListFromFolderHelper::class        => AbstractViewHelperFactory::class,
        ],
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
        ]
    ],
    'view_manager' => [
        'template_map' => [
            'layout/layoutFront'                         => __DIR__ . '/../view/layout/layoutFront.phtml',
            'layout/layoutMelis'                         => __DIR__ . '/../view/layout/layoutMelis.phtml',
            'melis-front/index/index'                    => __DIR__ . '/../view/melis-front/index/index.phtml',
            'MelisFront/dragdropzone'                    => __DIR__ . '/../view/melis-front/plugins/dragdropzone.phtml',
            'MelisFront/dragdropzone/meliscontainer'     => __DIR__ . '/../view/melis-front/plugins/dragdropzone-melis-container.phtml',
            'MelisFront/tag'                             => __DIR__ . '/../view/melis-front/plugins/tag.phtml',
            'MelisFront/tag/meliscontainer'              => __DIR__ . '/../view/melis-front/plugins/tag-melis-container.phtml',
            'MelisFront/menu'                            => __DIR__ . '/../view/melis-front/plugins/menu.phtml',
            'MelisFront/menu/melis/form'                 => __DIR__ . '/../view/melis-front/plugins/menu-melis-form.phtml',
            'MelisFront/breadcrumb'                      => __DIR__ . '/../view/melis-front/plugins/breadcrumb.phtml',
            'MelisFront/breadcrumb/melis/form'           => __DIR__ . '/../view/melis-front/plugins/breadcrumb-melis-form.phtml',
            'MelisFront/show-list-from-folder'           => __DIR__ . '/../view/melis-front/plugins/show-list-from-folder.phtml',
            'MelisFront/show-list/melis/form'            => __DIR__ . '/../view/melis-front/plugins/show-list-from-fold-form.phtml',
            'MelisFront/search-results'                  => __DIR__ . '/../view/melis-front/plugins/search-results.phtml',
            'MelisFront/search/melis/form'               => __DIR__ . '/../view/melis-front/plugins/search-melis-template-form.phtml',
            'MelisFront/list-paginator'                  => __DIR__ . '/../view/melis-front/plugins/list-paginator.phtml',
            'MelisFront/block-section'                   => __DIR__ . '/../view/melis-front/plugins/block-section.phtml',
            'MelisFront/block-section-container'         => __DIR__ . '/../view/melis-front/plugins/block-section-container.phtml',
            'MelisFront/gdpr-banner'                     => __DIR__ . '/../view/melis-front/plugins/gdpr-banner.phtml',
            'MelisFront/modal-template-form'             => __DIR__ . '/../view/melis-front/plugins/modal-template-form.phtml',
            //Mini Template plugins
            'MiniTemplate/Content'                      =>  __DIR__ . '/../view/melis-front/plugins/mini-template-default.phtml',
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
];
