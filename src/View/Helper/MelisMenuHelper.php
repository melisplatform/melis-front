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
 * This helper creates a menu inside the template to be used with the plugins
 *
 */
class MelisMenuHelper extends AbstractHelper
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
	
	
	public function __invoke($menuParameters)
	{
        $melisFrontMenuPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontMenuPlugin');
	    $melisFrontMenuPluginView = $melisFrontMenuPlugin->render($menuParameters);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $menuHtml = $viewRender->render($melisFrontMenuPluginView);

		return $menuHtml;
	}
}