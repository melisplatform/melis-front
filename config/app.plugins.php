<?php

return array(
    'plugins' => array(
        'melisfront' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'plugins' => array(
                'MelisFrontBreadcrumbPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisFront/breadcrumb',
                        'id' => 'breadcrumb',
                        'pageId' => 1,
                    ),
                    'melis' => array(
                    ),
                ),
                'MelisFrontMenuPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisFront/menu',
                        'id' => 'menu',
                        'pageId' => 1,
                    ),
                    'melis' => array(
                    ),
                ),
                'MelisFrontShowListFromFolderPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisFront/show-list-from-folder',
                        'id' => 'show-list-from-folder',
                        'pageId' => 1,
                    ),
                    'melis' => array(
                        
                    ),
                ),
                'MelisFrontSearchResultsPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisFront/search-results',
                        'id' => 'search-results',
                        'pageId' => 1,
                        'siteModuleName' => 'MySiteTest',
                        'keyword' => null,
                        // optional, if found will add a pagination object
                        'pagination' => array(
                            'current' => 1,
                            'nbPerPage' => 10,
                            'nbPageBeforeAfter' => 3
                        ),
                    ),
                    'melis' => array(
                    ),
                ),
             ),
        ),
     ),
);