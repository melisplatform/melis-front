<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Laminas\View\Model\ViewModel;
use MelisCore\Controller\AbstractActionController;

class StyleController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function getPagePluginWidthCssAction()
    {
        $response = $this->getResponse();

        $response->getHeaders()
            ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->addHeaderLine('Pragma'       , 'no-cache')
            ->addHeaderLine('Content-Type' , 'text/css;charset=UTF-8');

        $css     = '[class^="plugin-width"] {float: left;margin: 0;}';
        $pageId  = (int) $this->params()->fromQuery('idpage');

        $pageSvc = $this->getServiceManager()->get('MelisEnginePage');
        $data    = $pageSvc->getDatasPage($pageId);

        if($data) {

            $data = $data->getMelisPageTree()->page_content;

            $desktop = array(100 => 100);
            if(preg_match_all('/width_desktop=\"(.*?)\"/', $data, $matches)) {
                if(isset($matches[1]) && is_array($matches[1])) {
                    foreach($matches[1] as $widths)
                        $desktop[$widths] = $widths;
                }
            }
            $css .= $this->getMediaQuery(null, 981, $this->getClasses($desktop,'lg'), 'min').PHP_EOL;

            $tablet = array(100 => 100);
            if(preg_match_all('/width_tablet=\"(.*?)\"/', $data, $matches)) {
                if(isset($matches[1]) && is_array($matches[1])) {
                    foreach($matches[1] as $widths)
                        $tablet[$widths] = $widths;
                }
            }
            $css .= $this->getMediaQuery(481, 980, $this->getClasses($tablet, 'md')).PHP_EOL;

            $mobile = array(100 => 100);
            if(preg_match_all('/width_mobile=\"(.*?)\"/', $data, $matches)) {
                if(isset($matches[1]) && is_array($matches[1])) {
                    foreach($matches[1] as $widths)
                        $mobile[$widths] = $widths;


                }
            }
            $css .= $this->getMediaQuery(null, 480, $this->getClasses($mobile,'xs')).PHP_EOL;

        }

        $response->setContent($css);

        $view = new ViewModel();

        $view->setTerminal(true);
        $view->content = $response->getContent();

        return $view;

    }

    public function pluginWidthsAction()
    {
        $response = $this->getResponse();

        $response->getHeaders()
            ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->addHeaderLine('Pragma'       , 'no-cache')
            ->addHeaderLine('Content-Type' , 'text/css;charset=UTF-8');
        // text/css

        $css = null;

        $css = $this->getMediaQuery(null, 480, $this->getClasses('xs')).PHP_EOL;
        $css .= $this->getMediaQuery(481, 980, $this->getClasses('md')).PHP_EOL;
        $css .= $this->getMediaQuery(null, 981, $this->getClasses('lg'), 'min').PHP_EOL;
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
            $query  = "@media (min-width: {$minWidth}px) and (max-width: {$maxWidth}px)".PHP_EOL;
        }
        else {
            $query .= "@media only screen and ({$type}-width: {$maxWidth}px)".PHP_EOL;
        }

        $query .= "{".PHP_EOL;
        $query .= $classes;
        $query .= "}";

        return $query;
    }

    private function getClasses($widths = array(), $prefix = null)
    {
        $classes = null;
        $prefix  = !is_null($prefix) ? '-'.$prefix : null;

        // load the default CSS
        if(!$widths) {
            for($x = 1; $x <= 100.01; $x+=0.01) {
                $value     = number_format( round( (float) $x, 2), 2, '.', ',');
                $className = number_format( round( (float) $x, 2), 2, '-', ',');

                $classes .= "    .plugin-width{$prefix}-{$className} {".PHP_EOL;
                $classes .= "         width: {$value}%;".PHP_EOL;
                $classes .= "     }".PHP_EOL;
            }
        }
        else {
            // load specific CSS
            foreach($widths as $width) {
                $value     = number_format( round( (float) $width, 2), 2, '.', ',');
                $className = number_format( round( (float) $width, 2), 2, '-', ',');

                $classes .= "    .plugin-width{$prefix}-{$className} {".PHP_EOL;
                $classes .= "         width: {$value}%;".PHP_EOL;
                $classes .= "     }".PHP_EOL;
            }
        }


        return $classes;
    }


}