<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Navigation;

use Zend\Navigation\Service\DefaultNavigationFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Generate zend navigation based on Melis Page System
 *
 */
class MelisFrontNavigation extends DefaultNavigationFactory
{
	private $serviceLocator;
	private $idpage;
	private $renderMode;
	
	/**
	 * Constructor
	 * 
	 * @param ServiceManager $serviceLocator
	 * @param int $idpage
	 * @param string $renderMode
	 */
	public function __construct($serviceLocator, $idpage, $renderMode)
	{
		$this->setServiceLocator($serviceLocator);
		$this->idpage = $idpage;
		$this->renderMode = $renderMode;
	}
	
	public function setServiceLocator($serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
	public function getSiteMenu($siteId)
	{
	    $melisPage = $this->serviceLocator->get('MelisEnginePage');
	    $pageTree = $melisPage->getDatasPage($siteId);
	    
	    $pages = array();
	    
	    if (!is_null($pageTree))
	    {
	        $page = $this->formatPageInArray((Array)$pageTree->getMelisPageTree());
	         
	        $children = $this->getChildrenRecursive($siteId);
	        if (!empty($children))
	        {
	            $page['pages'] = $children;
	        }
	         
	        $pages[] = $page;
	    }
	    
	    return $pages;
	}
	
	/**
	 * Get subpages recursively
	 * 
	 * @param int $idPage
	 * @return array Pages
	 */
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
				$tmp = $this->formatPageInArray($page);
				$children = $this->getChildrenRecursive($page['tree_page_id']);
				if (!empty($children))
					$tmp['pages'] = $children;
				
				$results[] = $tmp;
			}
		}
		
		return $results;
	}
	
	public function formatPageInArray($page)
	{
		$melisTree = $this->serviceLocator->get('MelisEngineTree');
		
		if (empty($page['purl_page_url']))
		    $uri = $melisTree->getPageLink($page['tree_page_id'], 0);
		else
		    $uri = $page['purl_page_url'];
		
	    if (empty($page['page_edit_date']))
	        $page['page_edit_date'] = date('Y-m-d H:i:s');
	    
	    if (!empty($page['pseo_meta_title']))
	        $pageName = $page['pseo_meta_title'];
	    else
	        $pageName = $page['page_name'];
	        
	    $tmp = array(
	        'label' => $pageName,
	        'menu' => $page['page_menu'],
	        'uri' => $uri,
	        'idPage' => $page['tree_page_id'],
	        'lastEditDate' => $page['page_edit_date'],
	        'pageStat' => $page['page_status'],
	        'pageType' => $page['page_type'],
	    );
	    
	    if ($this->idpage == $page['tree_page_id'])
	        $tmp['active'] = true;
	    
	    return $tmp;
	}
	
	public function getSiteMainPageByPageId($idPage)
	{
	    $melisTree = $this->serviceLocator->get('MelisEngineTree');
	    $datasSite = $melisTree->getSiteByPageId($idPage);
	    
	    if (!empty($datasSite))
	       return $datasSite->site_main_page_id;
	    
	    return null;
	}
	
	/**
	 * Get Pages
	 * 
	 * @param ContainerInterface $container
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Navigation\Service\AbstractNavigationFactory::getPages()
	 */
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