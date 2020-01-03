<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2018 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service\Factory;

use MelisFront\Service\MelisTranslationService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MelisTranslationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $moduleSvc = $sl->get('MelisAssetManagerModulesService');
        $melisTranslationService = new MelisTranslationService($moduleSvc);
        $melisTranslationService->setServiceLocator($sl);

        return $melisTranslationService;
    }

}