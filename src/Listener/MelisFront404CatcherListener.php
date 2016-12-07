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
use Zend\Mvc\Router\Http\Segment;

/**
 * Site 404 catcher listener
 */
class MelisFront404CatcherListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_FINISH, 
        	function(MvcEvent $e){
        		$sm = $e->getApplication()->getServiceManager();
        		$router = $e->getRouter();
        		
        		$sm = $e->getApplication()->getServiceManager();
        		$routeMatch = $router->match($sm->get('request'));
        		
        		if (empty($routeMatch))
        		{
        		    // Retrieving the router Details, This will return URL details in Object
        		    $uri = $router->getRequestUri();
        		    
        		    // Retrieving Site 301 if the 404 data is exist as Old Url
        		    $site301 = $sm->get('MelisEngineTableSite301');
        		    $site301Data = $site301->getEntryByField('s301_old_url', $uri->getPath())->current();
        		    if (!empty($site301Data))
        		    {
        		        // Redirection
        		        $response = $e->getResponse();
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Location', $site301Data->s301_new_url));
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Cache-Control', 'no-cache, no-store, must-revalidate'));
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Pragma', 'no-cache'));
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Expires', false));
        		        $response->setStatusCode(301);
        		        $response->sendHeaders();
        		        exit ();
        		    }
        		}
        	},
        -1000);
        
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