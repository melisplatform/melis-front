<?php

namespace MelisFront\Navigation;

use Zend\Navigation\Service\DefaultNavigationFactory;
use Interop\Container\ContainerInterface;

/**
 * 
 * For customization, see here:
 * http://framework.zend.com/manual/current/en/tutorials/tutorial.navigation.html
 * 
 */
class MelisFrontNavigation extends DefaultNavigationFactory
{
	private $serviceLocator;
	private $idpage;
	private $renderMode;
	
	
	public function __construct($serviceLocator, $idpage, $renderMode)
	{
		$this->serviceLocator = $serviceLocator;
		$this->idpage = $idpage;
		$this->renderMode = $renderMode;
	}
	
	public function getChildrenRecursive($idPage)
	{
		$results = array();
		$melisTree = $this->serviceLocator->get('MelisEngineTree');
		
		$publishedOnly = 1;
		$pages = $melisTree->getPageChildren($idPage, $publishedOnly);
		
		if ($pages)
		{
			$pages = $pages->toArray();
			
			foreach ($pages as $page)
			{
				$uri = $melisTree->getPageLink($page['tree_page_id'], 1);
				
				$tmp = array(
						'label' => $page['page_name'],
						'menu' => $page['page_menu'],
						'uri' => $uri,
						'idPage' => $page['tree_page_id'],
				        'lastEditDate' => $page['page_edit_date'],
					    'pageStat' => $page['page_status'],
					    'pageType' => $page['page_type'],
				);
				
				if ($this->idpage == $page['tree_page_id'])
					$tmp['active'] = true;
				
				$children = $this->getChildrenRecursive($page['tree_page_id']);
				if (!empty($children))
					$tmp['pages'] = $children;
				
				$results[] = $tmp;
			}
		}
		
		return $results;
	}
	
	protected function getPages(ContainerInterface $container)
	{
		
		if (null === $this->pages) 
		{
			$siteMainId = 0;
			
			$melisPage = $this->serviceLocator->get('MelisEnginePage');
			$actualPage = $melisPage->getDatasPage($this->idpage);
			if ($actualPage)
			{
				$siteId = 0;
				$datasTemplate = $actualPage->getMelisTemplate();
				if (!empty($datasTemplate->tpl_site_id))
					$siteId = $datasTemplate->tpl_site_id;
				
				if (!empty($siteId) && $siteId > 0)
				{
					$melisTableSite = $this->serviceLocator->get('MelisEngineTableSite');
					$datasSite = $melisTableSite->getSiteById($siteId, getenv('MELIS_PLATFORM'));
					if (!empty($datasSite))
					{
						$datasSite = $datasSite->toArray();
						if (count($datasSite) > 0)
							$siteMainId = $datasSite[0]['site_main_page_id'];
					}
				}
			}

			$navigation = $this->getChildrenRecursive($siteMainId);
			
			$pages      = $this->getPagesFromConfig($navigation);
	
			$this->pages = $this->injectComponents(
					$pages
			);
		}
		
		return $this->pages;
	}
}