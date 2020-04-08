<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use MelisEngine\Service\MelisTreeService;
use Laminas\View\Helper\AbstractHelper;

class MelisPageLangVersionLinkHelper extends AbstractHelper
{
    public $serviceManager;
    
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param $idPage
     * @param $locale
     * @param $absolute
     * @return mixed
     */
    public function __invoke($idPage, $locale, $absolute = false)
    {
        /** @var MelisTreeService $melisTree */
        $melisTree = $this->serviceManager->get('MelisEngineTree');
        $link = $melisTree->getPageLinkByLocale($idPage, $locale, $absolute);
        
        return $link;
    }
}