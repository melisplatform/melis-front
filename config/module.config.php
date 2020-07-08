<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'melis-front' => array(
                'type'    => 'regex',
                'options' => array(
                    'regex'    => '.*/id/(?<idpage>[0-9]+)',
                    'defaults' => array(
                        'controller' => 'MelisFront\Controller\Index',
                        'action'     => 'index',
                        'renderType'     => 'melis_zf2_mvc',
                        'renderMode'     => 'front',
                        'preview'		=> false,
                    ),
                    'spec' => '%idpage'
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'melis_front_melisrender' => array(
                        'type'    => 'regex',
                        'options' => array(
                            'regex'    => '/renderMode/melis',
                            'defaults' => array(
                                'renderMode'     => 'melis',
                                'preview'		=> false,
                            ),
                            'spec' => ''
                        ),
                    ),
                    'melis_front_previewender' => array(
                        'type'    => 'regex',
                        'options' => array(
                            'regex'    => '/preview',
                            'defaults' => array(
                                'renderMode'     => 'melis',
                                'preview'		=> true,
                            ),
                            'spec' => ''
                        ),
                    ),
                ),
            ),
            'melis-front-special-urls' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller'     => 'MelisFront\Controller\SpecialUrls',
                    )
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'front-sitemap' => array(
                        'type'    => 'regex',
                        'options' => array(
                            'regex'    => 'sitemap.html|sitemap.xml|sitemap',
                            'defaults' => array(
                                'action' => 'sitemap',
                            ),
                            'spec' => ''
                        ),
                    ),
                    'front-plugin-widths' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'css/plugin-width.css',
                            'defaults' => array(
                                '__NAMESPACE__' => 'MelisFront\Controller',
                                'controller' => 'Style',
                                'action'     => 'pluginWidths',
                            ),
                        ),
                    ),
                    'front-page-plugin-widths' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'css/page-plugin-width.css',
                            'defaults' => array(
                                '__NAMESPACE__' => 'MelisFront\Controller',
                                'controller' => 'Style',
                                'action'     => 'getPagePluginWidthCss',
                            ),
                        ),
                    ),
                    'front-module-ctrl-action' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'MelisFront/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'MelisFront\Controller',
                            ),
                        ),
                    ),
                    'front-search-indexer' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'melissearchindex/module[/:moduleName]/pageid[/:pageid]/exclude-pageid[/:expageid]',
                            'constraints' => array(
                                'moduleName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'pageid'     => '[0-9]+',
                                'expageid'   => '[0-9;]+',
                            ),
                            'defaults' => array(
                                'controller'    => 'MelisFront\Controller\MelisFrontSearch',
                                'action'        => 'addLuceneIndex',
                            ),
                        ),
                    ),
                    'front-search-indexer-optimize' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'melisoptimizeindex/module[/:moduleName]',
                            'constraints' => array(
                                'moduleName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'MelisFront\Controller\MelisFrontSearch',
                                'action'        => 'optimizeIndex',
                            ),
                        ),
                    ),

                ),
            ),
            'melis-plugin-renderer' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/melispluginrenderer',
                    'defaults' => array(
                        'controller' => 'MelisFront\Controller\MelisPluginRenderer',
                        'action'     => 'getPlugin',
                        'renderMode'     => 'melis',
                        'preview'		=> false,
                    ),
                ),
            ),
            'sites-minify-assets' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    =>  '/minify-assets',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'MelisFront\Controller\MinifyAssets',
                        'action'     => 'minifyAssets',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'locale' => 'en_EN',
    ),
    'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'invokables' => array(

        ),
        'factories' => array(
            'MelisFrontHead' => 'MelisFront\Service\Factory\MelisFrontHeadServiceFactory',
            'MelisFrontNavigation' => 'MelisFront\Navigation\Factory\MelisFrontNavigationFactory',
            'MinifyAssets' => 'MelisFront\Service\Factory\MinifyAssetsServiceFactory',
            'MelisSiteTranslationService' => 'MelisFront\Service\Factory\MelisSiteTranslationServiceFactory',
            'MelisSiteConfigService' => 'MelisFront\Service\Factory\MelisSiteConfigServiceFactory',
            'MelisTranslationService' => 'MelisFront\Service\Factory\MelisTranslationServiceFactory',
            'MelisFront\Listener\MelisFront404To301Listener' => 'MelisFront\Listener\Factory\MelisFront404To301ListenerFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'MelisFront\Controller\Index' => 'MelisFront\Controller\IndexController',
            'MelisFront\Controller\SpecialUrls' => 'MelisFront\Controller\SpecialUrlsController',
            'MelisFront\Controller\MelisFrontSearch' => 'MelisFront\Controller\MelisFrontSearchController',
            'MelisFront\Controller\MelisPluginRenderer' => 'MelisFront\Controller\MelisPluginRendererController',
            'MelisFront\Controller\Style' => 'MelisFront\Controller\StyleController',
            'MelisFront\Controller\MinifyAssets' => 'MelisFront\Controller\MinifyAssetsController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'MelisFrontDragDropZonePlugin' => 'MelisFront\Controller\Plugin\MelisFrontDragDropZonePlugin',
            'MelisFrontTagHtmlPlugin' => 'MelisFront\Controller\Plugin\MelisFrontTagHtmlPlugin',
            'MelisFrontTagTextareaPlugin' => 'MelisFront\Controller\Plugin\MelisFrontTagTextareaPlugin',
            'MelisFrontTagMediaPlugin' => 'MelisFront\Controller\Plugin\MelisFrontTagMediaPlugin',
            'MelisFrontMenuPlugin' => 'MelisFront\Controller\Plugin\MelisFrontMenuPlugin',
            'MelisFrontBreadcrumbPlugin' => 'MelisFront\Controller\Plugin\MelisFrontBreadcrumbPlugin',
            'MelisFrontShowListFromFolderPlugin' => 'MelisFront\Controller\Plugin\MelisFrontShowListFromFolderPlugin',
            'MelisFrontSearchResultsPlugin' => 'MelisFront\Controller\Plugin\MelisFrontSearchResultsPlugin',
            'MelisFrontBlockSectionPlugin' => 'MelisFront\Controller\Plugin\MelisFrontBlockSectionPlugin',
            'MiniTemplatePlugin' => 'MelisFront\Controller\Plugin\MiniTemplatePlugin',
            'MelisFrontGdprBannerPlugin' => 'MelisFront\Controller\Plugin\MelisFrontGdprBannerPlugin',
            'MelisFrontGdprRevalidationPlugin' => 'MelisFront\Controller\Plugin\MelisFrontGdprRevalidationPlugin',
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'MelisDragDropZone' => 'MelisFront\View\Helper\Factory\MelisDragDropZoneHelperFactory',
            'MelisTag' => 'MelisFront\View\Helper\Factory\MelisTagsHelperFactory',
            'MelisMenu' => 'MelisFront\View\Helper\Factory\MelisMenuHelperFactory',
            'MelisLink' => 'MelisFront\View\Helper\Factory\MelisLinksHelperFactory',
            'MelisPageLangLink' => 'MelisFront\View\Helper\Factory\MelisPageLangVersionLinkHelperFactory',
            'MelisHomePageLink' => 'MelisFront\View\Helper\Factory\MelisHomePageLinkHelperFactory',
            'siteTranslate' => 'MelisFront\View\Helper\Factory\MelisSiteTranslationHelperFactory',
            'boTranslate' => 'MelisFront\View\Helper\Factory\MelisTranslationHelperFactory',
            'SiteConfig' => 'MelisFront\View\Helper\Factory\SiteConfigViewHelperFactory',
            'MelisGdprBannerPlugin' => 'MelisFront\View\Helper\Factory\MelisGdprBannerHelperFactory',
            'MelisListFromFolderPlugin' => 'MelisFront\View\Helper\Factory\MelisListFromFolderHelperFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
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
            'MelisFront/gdpr-revalidation'               => __DIR__ . '/../view/melis-front/plugins/gdpr-revalidation.phtml',

            //Mini Template plugins
            'MiniTemplate/Content'                      =>  __DIR__ . '/../view/melis-front/plugins/mini-template-default.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'aliases' => array(
                'MelisFront/' => __DIR__ . '/../public/',
            ),
        ),
    ),
    'caches' => [
        'melisfront_pages_file_cache' => array(
            'active' => true, // activate or deactivate Melis Cache for this conf
            'adapter' => array(
                'name'    => 'Filesystem',
                'options' => array(
                    'ttl' => 0, // 24hrs
                    'namespace' => 'melisfront_pages_file_cache',
                    'cache_dir' => $_SERVER['DOCUMENT_ROOT'] . '/../cache'
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => false),
                'Serializer'
            ),
            'ttls' => array(
                // add a specific ttl for a specific cache key (found via regexp)
                // 'my_cache_key' => 60,
            )
        ),
    ]
);
