<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use MelisFront\View\Helper\MelisHomePageLinkeHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MelisHomePageLinkHelperFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$serviceLoc = $sl->getServiceLocator();
		$helper = new MelisHomePageLinkeHelper($serviceLoc);
	    
	    return $helper;
	}

}