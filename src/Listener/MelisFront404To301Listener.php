<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\Event;

use MelisFront\Listener\MelisFrontSEODispatchRouterAbstractListener;
/**
 * This listener will react if page 404 is happen and this will try to redirect using new url
 */
class MelisFront404To301Listener extends MelisFrontSEODispatchRouterAbstractListener
{
    use MelisFrontSite301Trait;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
        	'*', 
            'melisfront_site_dispatch_ready',
        	function($e){

        	    $sm = $e->getTarget()->serviceManager;
        	    $params = $e->getParams();

                if (!empty($params['404'])) {
        	        // Retrieving the router Details, This will return URL details in Object
        	        $router = $sm->get('router');
        	        $uri = $router->getRequestUri();
        	        
        	        // Site 301 of the requested site only (Mantis #0009351: the first redirect with this
        	        // old url was used, whatever its site)
        	        $newUrl = $this->findSite301Url($sm, (string) $uri->getHost(), (string) $uri->getPath());
        	        if (!empty($newUrl)) {
        	            // If the 404 url exist on Database, the new Url will set to 301 to make a  redirect to new url
        	            $params['301_type'] = 'oldUrl301';
        	            $params['301'] = $newUrl;
        	            $params['404'] = null;
        	            $params['404_type'] = '';
        	        }
        	    }
        	},
        120
        );
    }
}