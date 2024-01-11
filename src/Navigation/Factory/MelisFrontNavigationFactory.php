<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Navigation\Factory;

use MelisFront\Navigation\MelisFrontNavigation;
use Psr\Container\ContainerInterface;

class MelisFrontNavigationFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
		$router = $container->get('router');
		$request = $container->get('request');
		$routeMatch = $router->match($request);
		
		if ($routeMatch) {
			$params = $routeMatch->getParams();
			$idpage = $params['idpage'];
			$renderMode = $params['renderMode'];
			
			$navigation = new MelisFrontNavigation($container, $idpage, $renderMode);
			$navigationService =  $navigation->createService($container);
		} else {
			$navigation = new MelisFrontNavigation($container, 0, 'front');
			$navigationService =  $navigation->createService($container);
		}
		
		return $navigationService;
	}
}