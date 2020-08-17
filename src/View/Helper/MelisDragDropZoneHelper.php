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
     * @param $idPage
     * @param $dragDropZoneId
     * @return mixed
     */
    public function __invoke($idPage, $dragDropZoneId)
	{
	    $melisFrontDragDropZonePlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontDragDropZonePlugin');

	    $melisFrontDragDropZonePluginView = $melisFrontDragDropZonePlugin->render([
	        'pageId' => $idPage,
	        'id' => $dragDropZoneId,
	    ]);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $tagHtml = $viewRender->render($melisFrontDragDropZonePluginView);

		return $tagHtml;
	}
}