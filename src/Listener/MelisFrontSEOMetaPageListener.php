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
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener will activate the replacement of the title and meta description
 * on Melis Pages
 * 
 */
class MelisFrontSEOMetaPageListener extends MelisGeneralListener implements ListenerAggregateInterface
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
				
					// Get the response generated
					$response = $e->getResponse();
					$content = $response->getContent();
				
					$params = $routeMatch->getParams();
					
					if (empty($params['idpage']))
						return;
					
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
}