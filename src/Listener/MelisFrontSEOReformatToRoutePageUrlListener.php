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
use Laminas\Router\Http\Segment;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener will check if a URL that does not respect the regular routing of 
 * a Melis Page is a Melis Page or not (ie not .../id/..).
 * It will look into the SEO of url defined for pages and try to match one.
 * If found, the route params supposed to be created for the page will be added,
 * as long as a special dynamic route to be able to differentiate the origin.
 * 
 */
class MelisFrontSEOReformatToRoutePageUrlListener extends MelisGeneralListener implements ListenerAggregateInterface
{
	public function attach(EventManagerInterface $events, $priority = 1)
	{
		$callBackHandler = $events->attach(
			MvcEvent::EVENT_ROUTE, 
			function(MvcEvent $e){
				$sm = $e->getApplication()->getServiceManager();
				$router = $e->getRouter();

				$routeMatch = $e->getRouteMatch();

				// AssetManager, we don't want listener to be executed if it's not a php code
				$uri = $_SERVER['REQUEST_URI'];
				preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
				if (count($matches) > 1)
					return;
				
				if (substr($uri, 0, 1) == '/')
					$uri = substr($uri, 1, strlen($uri));
				
				// Get the list of possible parameters
				$request = $e->getRequest();
				$getString = $request->getQuery()->toString();
				if ($getString != '')
				$getString = '?' . $getString;
				
				// Get the URL without the parameters to be able to compare
				$uri = str_replace($getString, '', $uri);
				
					
				// The SEO URLS will be effective only on declared domains for sites
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
//    		    $params = '';
//    		    $parameters = explode('/', $uri);
//    		    if (count($parameters) > 1)
//    		    {
//    		        for ($i = 1; $i < count($parameters); $i++)
//    		            $params .= '/' . $parameters[$i];
//    		            $uri = str_replace($params, '', $uri);
//    		    }
				
				// Trying to find an URL in Melis SEO
				$melisTablePageSeo = $sm->get('MelisEngineTablePageSeo');
				$datasPageSeo = $melisTablePageSeo->getEntryByField('pseo_url', $uri);
				if (!empty($datasPageSeo))
				{
					$datasPageSeo = $datasPageSeo->current();
					if (!empty($datasPageSeo))
					{
						$router = $e->getApplication()->getServiceManager()->get('router');
						
						// Creating dynamicaly the route and the params that are needed in the regular melis routing
						$route = Segment::factory(array(
							'route' => '/' . $uri,
							'defaults' => array(
								'controller' => 'MelisFront\Controller\Index',
								'action' => 'index',
								'idpage' => $datasPageSeo->pseo_id,
								'renderType' => 'melis_zf2_mvc',
								'renderMode' => 'front',
								'preview' => false,
								'urlparams' => $getString,
							)
						));
						
						// add the route to the router
						$router->addRoute('melis-front-page-seo', $route);
					}
				}
			},
		80);
		
		$this->listeners[] = $callBackHandler;
	}
}