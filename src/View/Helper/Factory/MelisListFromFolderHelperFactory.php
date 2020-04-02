<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use MelisFront\View\Helper\MelisListFromFolderHelper;

class MelisListFromFolderHelperFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$serviceLoc = $sl->getServiceLocator();
		$router = $serviceLoc->get('router');
		$request = $serviceLoc->get('request');
		$routeMatch = $router->match($request);
		
		if (!empty($routeMatch))
		{
		    $renderMode = $routeMatch->getParam('renderMode');
		    $preview = $routeMatch->getParam('preview');
		}
		else
		{
		    $renderMode = 'front';
		    $preview = false;
		}
		$helper = new MelisListFromFolderHelper($serviceLoc, $renderMode, $preview);
		
		return $helper;
	}

}