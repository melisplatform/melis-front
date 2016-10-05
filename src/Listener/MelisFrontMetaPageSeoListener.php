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

/**
 * This listener will activate when a page is deleted
 * 
 */
class MelisFrontMetaPageSeoListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_FINISH, 
        	function(MvcEvent $e){
        		
        	   // Get route match to know if we are displaying in back or front
            	$routeMatch = $e->getRouteMatch();
            	
            	$uri = $_SERVER['REQUEST_URI'];
        		preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
        		if (count($matches) > 1)
        		    return;
            	
            	if (!$routeMatch)
            		return;
            	 
            	$renderMode = $routeMatch->getParam('renderMode');
            	
            	if ($renderMode == 'melis' || $renderMode == 'front')
            	{
            		$sm = $e->getApplication()->getServiceManager();
            	
            		// Get the response generated
            		$response = $e->getResponse();
            		$content = $response->getContent();
            	
            		$params = $routeMatch->getParams();
            		$idpage = $params['idpage'];
            	
            		/**
            		 * Replace Head and SEO datas automatically
            		 */
            		$melisFrontHead = $sm->get('MelisFrontHead');
            		$newContent = $melisFrontHead->updateTitleAndDescription($idpage, $content);
            	
            		// Set the updated content
            		$response->setContent($newContent);
            	}
        	},
        110);
        
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