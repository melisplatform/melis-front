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
use Laminas\Mvc\Router\Http\Segment;

/**
 * Site 404 catcher listener
 */
class MelisFrontXSSParameterListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_ROUTE, 
        	function(MvcEvent $e){
        	    
        	    // AssetManager, we don't want listener to be executed if it's not a php code
        	    $uri = $_SERVER['REQUEST_URI'];
        	    preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
        	    if (count($matches) > 1)
        	        return;
        	    
        	    $request = $e->getRequest();
                $GetParameters = $request->getQuery();
            
        	    foreach ($GetParameters as $key => $value)
                {
                    if (!is_array($value))
                        $request->getQuery()->set($key, htmlspecialchars(htmlentities($value), ENT_QUOTES, 'UTF-8'));
                    else
                    {
                       
                        array_walk_recursive($value, function (&$val) {
                            $val = htmlentities($val);
                            $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        });
                          
                        $request->getQuery()->set($key, $value);
                    }
                }
        	},
        100);
        
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