<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper\Factory;

use MelisFront\View\Helper\MelisDragDropZoneHelper;
use Psr\Container\ContainerInterface;

class AbstractViewHelperFactory
{
    public function __invoke(ContainerInterface $container, $targetName)
    {
        $isntance = new $targetName($container);
        return $container;
    }
}