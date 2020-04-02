<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service\Factory;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use MelisFront\Service\MelisFrontHeadService;

class MelisFrontHeadServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$melisMelisFrontHeadService = new MelisFrontHeadService();
		$melisMelisFrontHeadService->setServiceLocator($sl);
		
		return $melisMelisFrontHeadService;
	}

}