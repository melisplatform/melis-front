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
 * This listener will activate when a page is deleted
 * 
 */
class MelisFrontReformatToRoutePageSeoUrlListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_ROUTE, 
        	function(MvcEvent $e){
        		$sm = $e->getApplication()->getServiceManager();
        		$router = $e->getRouter();

        		$routeMatch = $e->getRouteMatch();
        		
        		$uri = $_SERVER['REQUEST_URI'];
        		preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
        		if (count($matches) > 1)
        		    return;
        		
        		if (substr($uri, 0, 1) == '/')
        		    $uri = substr($uri, 1, strlen($uri));
        		
        		$request = $e->getRequest();
        		$getString = $request->getQuery()->toString();
        		if ($getString != '')
        		  $getString = '?' . $getString;
        		
        		$uri = str_replace($getString, '', $uri);
        		  
        		     
    		    // The SEO URLS will be effective only
    		    $domain = $_SERVER['SERVER_NAME'];
    		    $melisTableDomain = $sm->get('MelisEngineTableSiteDomain');
    		    $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain);
    		    if (empty($datasDomain) || empty($datasDomain->current()) || empty($uri))
    		    {
    		        // We are not on a front site, then we don't use SEO URLS (also to
    		        // avoid collision with BO modules rules)
    		        return;
    		    }
    		     
    		    // Removing the optional parameters from url before checking
    		    $params = '';
    		    $parameters = explode('/', $uri);
    		    if (count($parameters) > 1)
    		    {
    		        for ($i = 1; $i < count($parameters); $i++)
    		            $params .= '/' . $parameters[$i];
    		            $uri = str_replace($params, '', $uri);
    		    }
    		     
    		    $melisTablePageSeo = $sm->get('MelisEngineTablePageSeo');
    		    $datasPageSeo = $melisTablePageSeo->getEntryByField('pseo_url', $uri);
    		    if (!empty($datasPageSeo))
    		    {
    		        $datasPageSeo = $datasPageSeo->current();
    		        if (!empty($datasPageSeo))
    		        {
    		            $router = $e->getApplication()->getServiceManager()->get('router');
    		             
    		            $route = Segment::factory(array(
    		                'route' => '/' . $uri,
    		                'defaults' => array(
    		                    'controller' => 'MelisFront\Controller\Index',
    		                    'action' => 'index',
    		                    'idpage' => $datasPageSeo->pseo_id,
    		                    'renderType' => 'melis_zf2_mvc',
    		                    'renderMode' => 'front',
    		                    'preview' => false,
    		                    'urlparams' => $params,
    		                )
    		            ));
    		
    		            // add it to the router
    		            $router->addRoute('melis-front-page-seo', $route);
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