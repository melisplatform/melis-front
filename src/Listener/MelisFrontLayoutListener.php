<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
/**
 * This listener choose to apply or not the Melis layout for the templates shown
 * resulting in adding JS scripts for TinyMCE.
 *
 */
class MelisFrontLayoutListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
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
                        
                $renderMode = $routeMatch->getParam('renderMode');
                $previewMode = $routeMatch->getParam('preview');
                
                // Only for Melis Front or Back routes
                if ($renderMode == 'melis' || $renderMode == 'front')
                {
                    $sm = $e->getApplication()->getServiceManager();
                    
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
                        $renderer = $sm->get('viewrenderer');
                        $finalView = new ViewModel();
                        $finalView->content = $content;
                        $finalView->idPage = $idpage;
                        $finalView->setTerminal(true);
                        if ($renderMode == 'melis')
                        {
                            $siteModule = getenv('MELIS_MODULE');
                            $melisPage = $sm->get('MelisEnginePage');
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
                            
                            $forwardPlugin = $sm->get('ControllerPluginManager')->get('Forward');
                            
                            // Including the plugins menu by getting the view
                            $pluginsMenuView = $forwardPlugin->dispatch('MelisCms\Controller\FrontPlugins',
                                array('action' => 'renderPluginsMenu', 'siteModule' => $siteModule));
                            
                            $viewRender = $sm->get('ViewRenderer');
                            
                            if (!$previewMode)
                                $finalView->pluginsMenu = $viewRender->render($pluginsMenuView);
                                
                        }
                        else
                            $finalView->setTemplate('layout/layoutFront');
                            
                        // Auto adding plugins Melis CSS and JS files to layout
                        if ($sm->get('templating_plugins')->hasItem('plugins_melis'))
                            $files = $sm->get('templating_plugins')->getItem('plugins_melis');
                        else
                            $files = array();
                                    
                        // variable Pre-decliration to init js and css indexes
                        $assets = array(
                            'js' => array(),
                            'css' => array(),
                        );
                        
                        // add dynamic assets
                        $config = $sm->get('config');
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
                        $response->setContent($newContent);
                    }
                }
            },
            80);
        
        $this->listeners[] = $callBackHandler;
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}