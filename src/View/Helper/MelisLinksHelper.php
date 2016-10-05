<?php

namespace MelisFront\View\Helper;

use Zend\View\Helper\AbstractHelper;

class MelisLinksHelper extends AbstractHelper
{
	public $serviceManager;
	public $dataBreadcrumbs;

	public function __construct($sm)
	{
		$this->serviceManager = $sm;
		$this->dataBreadcrumbs = array();
	}
	
	public function __invoke($idPage, $absolute)
	{
		if (empty($this->dataBreadcrumbs[$idPage]))
		{
			$melisTree = $this->serviceManager->get('MelisEngineTree');
			$link = $melisTree->getPageLink($idPage, $absolute);
			$this->dataBreadcrumbs[$idPage] = $link;
		}
		else
		{
			$link = $this->dataBreadcrumbs[$idPage];
		}
		
		return $link;
	}
}