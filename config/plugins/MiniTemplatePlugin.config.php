<?php
return array(
    'front' => array(
        'template_path' => array('MiniTemplate/Content'),
        'id' => 'tag-miniTpl',
        'pageId' => 1,
        'type' => 'html',
        'default' => null,
        'value' => '',
        'widthDesktop' => 100,
        'widthTablet'  => 100,
        'widthMobile'  => 100,
        'pluginContainerId' => null,
        'files' => array(
            'css' => array(
            ),
            'js' => array(
            ),
        ),
    ),
    'melis' => array(
        'subcategory' => array(
            'id' => 'miniTemplatePlugins',
            'title' => 'Site Name'
        ),
        'name' => 'Mini Template Name',
        'thumbnail' => null,
        'description' => null,
        'files' => array(
            'css' => array(
            ),
            'js' => array(
                'js_melistag' => '/MelisFront/plugins/js/plugin.melistagHTML.init.js'
            ),
        ),
        'js_initialization' => array(),
    ),
);