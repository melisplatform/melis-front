<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service\Factory;

use MelisFront\Service\MinifyAssetsService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MinifyAssetsServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$minifyAssets = new MinifyAssetsService();
        $minifyAssets->setServiceLocator($sl);
		
		return $minifyAssets;
	}

}