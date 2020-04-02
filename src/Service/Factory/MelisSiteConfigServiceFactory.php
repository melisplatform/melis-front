<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service\Factory;

use MelisFront\Service\MelisSiteConfigService;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\FactoryInterface;

class MelisSiteConfigServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $melisSiteConfigService = new MelisSiteConfigService();
        $melisSiteConfigService->setServiceLocator($sl);

        return $melisSiteConfigService;
    }
}