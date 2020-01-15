<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * Creates a list from folder
 *
 */
class MelisListFromFolderHelper extends AbstractHelper
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
	
	
	public function __invoke($parameters)
	{
        $listPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontShowListFromFolderPlugin');
	    $listPluginView = $listPlugin->render($parameters);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $listHtml = $viewRender->render($listPluginView);

		return $listHtml;
	}
}