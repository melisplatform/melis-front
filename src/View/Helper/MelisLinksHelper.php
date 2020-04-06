<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;

/**
 * This helper will generate links for a melis page
 */
class MelisLinksHelper extends AbstractHelper
{
	public $serviceManager;
	public $dataBreadcrumbs = [];

	public function __construct(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * 
	 * @param int $idPage Id of the page for the link
	 * @param boolean $absolute Add the domain if true
	 */
	public function __invoke($idPage, $absolute)
	{
		if (empty($this->dataBreadcrumbs[$idPage])) {
			$melisTree = $this->serviceManager->get('MelisEngineTree');
            $link = $melisTree->getPageLink($idPage, $absolute);
			$this->dataBreadcrumbs[$idPage] = $link;
		} else {
			$link = $this->dataBreadcrumbs[$idPage];
		}
		
		return $link;
	}
}