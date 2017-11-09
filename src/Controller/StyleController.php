<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
class StyleController extends AbstractActionController
{
    public function pluginWidthsAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Content-Type:', 'text/css');
        // text/css

        $css = null;

        $css = $this->getMediaQuery(null, 480, $this->getClasses('xs'));
        $css .= $this->getMediaQuery(481, 980, $this->getClasses('md'));
        $css .= $this->getMediaQuery(null, 981, $this->getClasses('lg'), 'min');
        $response->setContent($css);

        $view = new ViewModel();

        $view->setTerminal(true);
        $view->content = $response->getContent();

        return $view;
    }

    private function getMediaQuery($minWidth = null, $maxWidth, $classes = null, $type = 'max')
    {
        $query  = null;
        if(!is_null($minWidth)) {
            $query = "@media (min-width: {$minWidth}px) and (max-width: {$maxWidth}px)";
        }
        else {
            $query  .= "@media only screen and ({$type}-width: {$maxWidth}px)";
        }

        $query .= "{";
        $query .= $classes;
        $query .= "}";

        return $query;
    }

    private function getClasses($prefix = null)
    {
        $classes = null;
        $prefix  = !is_null($prefix) ? '-'.$prefix : null;

        for($x = 1; $x <= 100; $x++) {
            $classes .= ".plugin-width{$prefix}-{$x} {";
            $classes .= "width: {$x}%;";
            $classes .= "}";
        }

        return $classes;
    }
    private function getObjects($prefix = null)
    {
        $classes = null;
        $prefix  = null;
        for($x = 1; $x <= 100; $x++) {
            $classes .= ".plugin-width{$prefix}-{$x} {";

        }

        return $classes;
    }
}