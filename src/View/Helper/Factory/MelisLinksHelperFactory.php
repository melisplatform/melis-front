<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use MelisFront\View\Helper\MelisLinksHelper;

class MelisLinksHelperFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$serviceLoc = $sl->getServiceLocator();
	    $helper = new MelisLinksHelper($serviceLoc);
	    
	    return $helper;
	}

}