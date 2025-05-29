<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

/**
 * This helper creates a dragdropzone inside the template to be used with the plugins
 *
 */
class MelisDragDropZoneHelper extends AbstractHelper
{
    public $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param $pageId
     * @param $dragDropZoneId
     * @return mixed
     */
    public function __invoke($pageId, $dragDropZoneId, $isInnerDragDropZone = false)
    {
        $viewRender = $this->serviceManager->get('ViewRenderer');
        $dndPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontDragDropZonePlugin');

        if (!$isInnerDragDropZone) {

            $dndView = new ViewModel();
            $dndView->setTemplate('MelisFront/dnd');
            $dnds = [];

            $outDndConfig = $dndPlugin->render([
                'pageId' => $pageId,
                'id' => $dragDropZoneId,
                'isInnerDragDropZone' => false
            ]);

            $pluginVars = $outDndConfig->getVariables();
            $xml = $pluginVars['pluginConfig']['xmldbvalues'];
            $xmlData = '';

            if (is_array($xml)) {

                $xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                $xmlData .= '<document type="MelisCMS" author="MelisTechnology" version="2.0">' . "\n";

                foreach ($xml as $val)
                    $xmlData .= $val;

                $xmlData .= '</document>';
            } else
                $xmlData = $xml;

            $xmlData = simplexml_load_string($xmlData);

            if ($xmlData)
                foreach ($xmlData as $k => $dnd) {

                    if ($k == 'melisDragDropZone') {

                        $id = (string) $dnd->attributes()->id;
                        $referer = (string) $dnd->attributes()->plugin_referer;

                        if (in_array($dragDropZoneId, [$id, $referer])) {

                            $dnds[] = [
                                'pageId' => $pageId,
                                'id' => $id,
                            ];
                        }
                    }
                }


            if (empty($dnds)) {

                // create initial dnd zone
                $dnds[] = [
                    'pageId' => $pageId,
                    'id' => $dragDropZoneId,
                ];

                // save initial dnd xml data to session
                $container = new Container('meliscms');
                if (!isset($container['content-pages'][$pageId]))
                    $container['content-pages'][$pageId] = [];

                if (isset($container['content-pages'][$pageId])) {
                    if (!isset($container['content-pages'][$pageId]['melisDragDropZone']))
                        $container['content-pages'][$pageId]['melisDragDropZone'] = [];

                    if (isset($container['content-pages'][$pageId]['melisDragDropZone'])) {
                        if (!isset($container['content-pages'][$pageId]['melisDragDropZone'][$dragDropZoneId])) {
                            $xml = '<melisDragDropZone id="' . $dragDropZoneId . '" plugin_container_id="' . $dragDropZoneId . '" plugin_referer="" plugin_position=""></melisDragDropZone>';
                            $container['content-pages'][$pageId]['melisDragDropZone'][$dragDropZoneId] = $xml;
                        }
                    }
                }
            }

            $dndView->dnds = $dnds;

            return $viewRender->render($dndView);
        } else {

            $dndPluginView = $dndPlugin->render([
                'pageId' => $pageId,
                'id' => $dragDropZoneId,
                'isInnerDragDropZone' => true
            ]);

            $tagHtml = $viewRender->render($dndPluginView);
            return $tagHtml;
        }
    }
}
