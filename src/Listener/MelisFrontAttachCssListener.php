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

/**
 * This listener choose to apply or not the Melis layout for the templates shown
 * resulting in adding JS scripts for TinyMCE.
 * 
 */
class MelisFrontAttachCssListener implements ListenerAggregateInterface
{
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
            	
            	$renderMode = $routeMatch->getParam('renderMode');
        		
            	// Only for Melis Front or Back routes
            	if ($renderMode == 'melis' || $renderMode == 'front')
            	{
        	    	$sm = $e->getApplication()->getServiceManager();
        	        
        	    	$params = $routeMatch->getParams();
        	    	
        	    	if (!empty($params['idpage']))
        	    	{
        	    	    $idpage = $params['idpage'];
        	    	    
        	    	    // Get the content generated
        	    	    $response = $e->getResponse();
        	    	    $content = $response->getContent();
        	    	    
        	    	    $styleService = $sm->get('MelisEngineStyle');
        	    	    
        	    	    // get page specific styles
        	    	    $pageStylesData = $styleService->getStyles($idpage, true);
        	    	    
        	    	    if(!empty($pageStylesData)){
        	    	        
        	    	        $newLinks = array();
        	    	        
        	    	        // format each style data to html tag
        	    	        foreach($pageStylesData as $style){
        	    	            $newLinks[] = '<link href="'.$style['style_path'].'" media="screen" rel="stylesheet" type="text/css">';
        	    	        }
        	    	        
        	    	        $newLinks  = implode('', $newLinks);
        	    	        
        	    	        // get the last link
        	    	        $returnValue = preg_match_all('/<link(.|\\n)*?>/', $content, $m, PREG_SET_ORDER);
        	    	        $lastLink  = end($m)[0];
        	    	        
        	    	        // replace last link and append last and new links
        	    	        $newContent = str_replace($lastLink, $lastLink.$newLinks, $content);
        	    	        
        	    	        $response->setContent($newContent);
        	    	    }
        	    	}
            	}
        	},
        90);
        
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