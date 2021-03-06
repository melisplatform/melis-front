<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Laminas\Json\Json;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener choose to apply or not the Melis layout for the templates shown
 * resulting in adding JS scripts for TinyMCE.
 *
 */
class MelisFrontLayoutListener extends MelisGeneralListener implements ListenerAggregateInterface
{
    public $serviceManager;

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_FINISH,
            function(MvcEvent $e){

                // Get route match to know if we are displaying in back or front
                $routeMatch = $e->getRouteMatch();

                // AssetManager, we don't want listener to be executed if it's not a php code
                $uri = $_SERVER['REQUEST_URI'];
                preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
                if (count($matches) > 1)
                    return;

                // No routematch, we're not in Melis, no need this listener
                if (!$routeMatch)
                    return;

                $renderMode  = $routeMatch->getParam('renderMode');
                $previewMode = $routeMatch->getParam('preview');
                $idpage      = $routeMatch->getParam('idpage');

                // Only for Melis Front or Back routes
                if ($renderMode == 'melis' || $renderMode == 'front')
                {
                    $this->serviceManager = $e->getApplication()->getServiceManager();

                    // Get the response generated
                    $response = $e->getResponse();

                    $content  = $response->getContent();
                    $request  = $e->getRequest();

                    $isJson   = false;
                    if($request->isPost()) {
                        $regexJsonChecker  = '/
                            (?(DEFINE)
                            (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
                            (?<boolean>   true | false | null )
                            (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
                            (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
                            (?<pair>      \s* (?&string) \s* : (?&json)  )
                            (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
                            (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
                            )\A (?&json) \Z/six';
                        // check whether the content is json or an html content
                        if(preg_match($regexJsonChecker, $content)) {
                            $jsonContent = (array) Json::decode($content);
                            if($isJson &&  isset($jsonContent['isJson']) && ($jsonContent['isJson'])) {
                                // return the JSON content
                                return $jsonContent;
                            }
                            $isJson = true;
                        }
                    }

                    if(!$isJson) {

                        // if not, then just return the regular html content
                        $params = $routeMatch->getParams();

                        if (empty($params['idpage']))
                            return;
                        $idpage = $params['idpage'];

                        /**
                         * Use the view renderer to:
                         * - add Melis Version and time generation in front
                         * - add TinyMce files for edition when the page is looked in the back
                         */
                        $renderer = $this->getServiceManager()->get('ViewRenderer');
                        $finalView = new ViewModel();
                        $finalView->content = $content;
                        $finalView->idPage = $idpage;
                        $finalView->setTerminal(true);
                        if ($renderMode == 'melis')
                        {
                            $siteModule = getenv('MELIS_MODULE');
                            $melisPage = $this->getServiceManager()->get('MelisEnginePage');
                            $datasPage = $melisPage->getDatasPage($idpage, 'saved');

                            if($datasPage)
                            {
                                $datasTemplate = $datasPage->getMelisTemplate();

                                if(!empty($datasTemplate))
                                {
                                    $siteModule = $datasTemplate->tpl_zf2_website_folder;
                                }
                            }

                            // Setting special layout
                            $finalView->setTemplate('layout/layoutMelis');
                            $forwardPlugin = $this->getServiceManager()->get('ControllerPluginManager')->get('Forward');

                            // Including the plugins menu by getting the view
                            $pluginsMenuView = $forwardPlugin->dispatch('MelisCms\Controller\FrontPlugins',
                                array('action' => 'renderPluginsMenu', 'siteModule' => $siteModule, 'pageId' => $idpage));

                            $viewRender = $this->getServiceManager()->get('ViewRenderer');

                            if (!$previewMode)
                                $finalView->pluginsMenu = $viewRender->render($pluginsMenuView);

                        }
                        else
                            $finalView->setTemplate('layout/layoutFront');

                        // Auto adding plugins Melis CSS and JS files to layout
                        if ($this->getServiceManager()->get('templating_plugins')->hasItem('plugins_melis'))
                            $files = $this->getServiceManager()->get('templating_plugins')->getItem('plugins_melis');
                        else
                            $files = array();

                        // variable Pre-decliration to init js and css indexes
                        $assets = array(
                            'js' => array(),
                            'css' => array(),
                        );

                        // add dynamic assets
                        $config = $this->getServiceManager()->get('config');
                        $assets = array_merge($assets, $config['plugins']['melisfront']['resources']);

                        if (!empty($files['css'])){
                            $assets['css'] = array_merge($assets['css'], $files['css']);
                        }

                        if (!empty($files['js'])){
                            $assets['js'] = array_merge($assets['js'], $files['js']);
                        }

                        $finalView->setVariable('assets', $assets);
                        $finalView->pluginsMelisFiles = $files;

                        $newContent = $renderer->render($finalView);

                        // Set the updated content
                        $em = $e->getApplication()->getEventManager();
                        $tmp = $em->trigger('melis_front_layout', $this, array('content' => $newContent, 'idPage' => $idpage));
                        if($tmp && isset($tmp[0])) {
                            if($tmp->offsetGet(0)) {
                                $newContent = $tmp->offsetGet(0);
                            }
                        }

                        // add plugin style css if page is viewed in front
                        if($renderMode == 'front')
                            $newContent = str_replace('</head>',
                                '<link href="/css/page-plugin-width.css?idpage='.$idpage.'" media="screen" rel="stylesheet" type="text/css"></head>', $newContent);

                        $response->setContent($newContent);
                    }
                }
            },
            80);

        $this->listeners[] = $callBackHandler;
    }
}