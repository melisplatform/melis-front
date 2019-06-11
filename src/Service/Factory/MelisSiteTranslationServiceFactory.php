<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2018 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service\Factory;

use MelisFront\Service\MelisSiteTranslationService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MelisSiteTranslationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $melisSiteTranslationService = new MelisSiteTranslationService();
        $melisSiteTranslationService->setServiceLocator($sl);
        return $melisSiteTranslationService;
    }

}