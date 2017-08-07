<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * This helper creates a dragdropzone inside the template to be used with the plugins
 *
 */
class MelisDragDropZoneHelper extends AbstractHelper
{
	public $serviceManager;
	public $renderMode;
	public $preview;

	public function __construct($sm, $renderMode, $preview)
	{
		$this->serviceManager = $sm;
		$this->renderMode = $renderMode;
		$this->preview = $preview;
	}
	
	
	public function __invoke($idPage, $dragDropZoneId)
	{
	    $melisFrontDragDropZonePlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontDragDropZonePlugin');

	    $melisFrontDragDropZonePluginView = $melisFrontDragDropZonePlugin->render(array(
	        'pageId' => $idPage,
	        'id' => $dragDropZoneId,
	    ));
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $tagHtml = $viewRender->render($melisFrontDragDropZonePluginView);

		return $tagHtml;
	}
}