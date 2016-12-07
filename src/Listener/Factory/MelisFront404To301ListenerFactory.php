<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use MelisFront\Listener\MelisFront404To301Listener;

class MelisFront404To301ListenerFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{ 
    	$melisFront404To301Listener = new MelisFront404To301Listener($sl);
	    return $melisFront404To301Listener;
	}
}