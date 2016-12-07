<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use MelisFront\Listener\MelisFrontSEODispatchRouterAbstractListener;
/**
 * This listener will react if page 404 is happen and this will try to redirect using new url
 */
class MelisFront404To301Listener extends MelisFrontSEODispatchRouterAbstractListener implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    
    public function setServiceLocator(ServiceLocatorInterface $sl)
    {
        $this->serviceLocator = $sl;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();
        
        $callBackHandler = $sharedEvents->attach(
        	'*', 
            'melisfront_site_dispatch_ready',
        	function($e){
        		
        	    $sm = $this->serviceLocator;
        	    $params = $e->getParams();
        	    
                if (!empty($params['404']))
        	    {
        	        // Retrieving the router Details, This will return URL details in Object
        	        $router = $sm->get('router');
        	        $uri = $router->getRequestUri();
        	        
        	        // Retrieving Site 301 if the 404 data is exist as Old Url
        	        $site301 = $sm->get('MelisEngineTableSite301');
        	        $site301Data = $site301->getEntryByField('s301_old_url', $uri->getPath())->current();
        	        if (!empty($site301Data))
        	        {
        	            // If the 404 url exist on Database, the new Url will set to 301 to make a  redirect to new url
        	            $params['301_type'] = 'oldUrl301';
        	            $params['301'] = $site301Data->s301_new_url;
        	            $params['404'] = null;
        	            $params['404_type'] = '';
        	        }
        	    }
        	},
        120);
        
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