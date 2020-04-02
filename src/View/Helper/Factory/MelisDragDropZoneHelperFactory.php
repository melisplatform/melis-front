<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use MelisFront\View\Helper\MelisDragDropZoneHelper;

class MelisDragDropZoneHelperFactory implements FactoryInterface
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
		$helper = new MelisDragDropZoneHelper($serviceLoc, $renderMode, $preview);
		
		return $helper;
	}

}