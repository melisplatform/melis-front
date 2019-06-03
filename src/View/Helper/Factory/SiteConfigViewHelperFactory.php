<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use MelisFront\View\Helper\SiteConfigViewHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class SiteConfigViewHelperFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$serviceLoc = $sl->getServiceLocator();
		$helper = new SiteConfigViewHelper($serviceLoc);
	    
	    return $helper;
	}

}