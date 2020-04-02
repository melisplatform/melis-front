<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Navigation\Factory;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use MelisFront\Navigation\MelisFrontNavigation;

class MelisFrontNavigationFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$router = $sl->get('router');
		$request = $sl->get('request');
		$routeMatch = $router->match($request);
		
		if ($routeMatch)
		{
			$params = $routeMatch->getParams();
			$idpage = $params['idpage'];
			$renderMode = $params['renderMode'];
			
			$navigation = new MelisFrontNavigation($sl, $idpage, $renderMode);
			$navigationService =  $navigation->createService($sl);
		}
		else
		{
			$navigation = new MelisFrontNavigation($sl, 0, 'front');
			$navigationService =  $navigation->createService($sl);
		}
		
		return $navigationService;
	}

}