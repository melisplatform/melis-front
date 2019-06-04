<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use MelisEngine\Service\MelisTreeService;
use Zend\View\Helper\AbstractHelper;

class MelisHomePageLinkeHelper extends AbstractHelper
{
    public $serviceManager;
    
    public function __construct($sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @param $idPage
     * @param $absolute
     * @return mixed
     */
    public function __invoke($idPage, $absolute = false)
    {
        /** @var MelisTreeService $melisTree */
        $melisTree = $this->serviceManager->get('MelisEngineTree');
        $link = $melisTree->getHomePageLink($idPage, $absolute);
        
        return $link;
    }
}