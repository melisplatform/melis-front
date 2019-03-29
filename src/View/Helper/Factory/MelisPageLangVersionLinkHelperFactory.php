<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use MelisFront\View\Helper\MelisPageLangVersionLinkHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MelisPageLangVersionLinkHelperFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$serviceLoc = $sl->getServiceLocator();
		$helper = new MelisPageLangVersionLinkHelper($serviceLoc);
	    
	    return $helper;
	}

}