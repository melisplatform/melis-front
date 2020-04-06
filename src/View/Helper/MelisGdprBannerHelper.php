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
 * Creates a GDPR Banner
 *
 */
class MelisGdprBannerHelper extends AbstractHelper
{
	public $serviceManager;
//	public $renderMode;
//	public $preview;

	public function __construct(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
//		$this->renderMode = $renderMode;
//		$this->preview = $preview;
	}

	public function __invoke($bannerParameters)
	{
        $melisGdprBannerPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisFrontGdprBannerPlugin');
	    $melisGdprBannerPluginView = $melisGdprBannerPlugin->render($bannerParameters);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $bannerHtml = $viewRender->render($melisGdprBannerPluginView);

		return $bannerHtml;
	}
}