<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

/**
 * Creates a list from folder
 *
 */
class MelisListFromFolderHelper extends AbstractHelper
{
	public $serviceManager;
//	public $renderMode;
//	public $preview;

	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
//		$this->renderMode = $renderMode;
//		$this->preview = $preview;
	}
	
	
	public function __invoke($parameters)
	{
        $listPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontShowListFromFolderPlugin');
	    $listPluginView = $listPlugin->render($parameters);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $listHtml = $viewRender->render($listPluginView);

		return $listHtml;
	}
}