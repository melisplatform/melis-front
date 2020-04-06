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
//	public $renderMode;
//	public $preview;

	public function __construct(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;

        /*$router = $serviceManager->get('router');
        $request = $serviceManager->get('request');
        $routeMatch = $router->match($request);

        if (!empty($routeMatch)) {
            $renderMode = $routeMatch->getParam('renderMode');
            $preview = $routeMatch->getParam('preview');
        } else {
            $renderMode = 'front';
            $preview = false;
        }

		$this->renderMode = $renderMode;
		$this->preview = $preview;*/
	}
	
	
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