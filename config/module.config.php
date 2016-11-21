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
                    'front-module-ctrl-action' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'MelisFront/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
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
		),
	),
    'controllers' => array(
        'invokables' => array(
            'MelisFront\Controller\Index' => 'MelisFront\Controller\IndexController',
            'MelisFront\Controller\SpecialUrls' => 'MelisFront\Controller\SpecialUrlsController',
            'MelisFront\Controller\MelisFrontSearch' => 'MelisFront\Controller\MelisFrontSearchController',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'MelisTag' => 'MelisFront\View\Helper\Factory\MelisTagsHelperFactory',
            'MelisLink' => 'MelisFront\View\Helper\Factory\MelisLinksHelperFactory',
        ),    
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
           'layout/layoutFront'           => __DIR__ . '/../view/layout/layoutFront.phtml',
           'layout/layoutMelis'           => __DIR__ . '/../view/layout/layoutMelis.phtml',
            'melis-front/index/index' => __DIR__ . '/../view/melis-front/index/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
