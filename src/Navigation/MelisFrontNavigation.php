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
	    $uri = $melisTree->getPageLink($page['tree_page_id'], 1);
	    
	    if (empty($page['page_edit_date']))
	        $page['page_edit_date'] = date('Y-m-d H:i:s');
	    
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
	    
	    return $tmp;
	}
	
	/**
	 * Getting the Site menu or the Site pageTree
	 * 
	 * @return Array of MelisPageSaved/MelisPagePublished Object|null
	 */
	public function getSiteMenuByPageId()
	{
	    $menu = array();
	    // Template Id of the Page
	    $tplId = null;
	    
	    // Table services
	    $pagePublishTbl = $this->serviceLocator->get('MelisEngineTablePagePublished');
	    $pageSaveTbl = $this->serviceLocator->get('MelisEngineTablePageSaved');
	    $tplTbl = $this->serviceLocator->get('MelisEngineTableTemplate');
	    $siteTbl = $this->serviceLocator->get('MelisEngineTableSite');
	    // Checking renderMode melis/front
	    if ($this->renderMode == 'front')
	    {
	        // In the Front only Publeshed pages are accessable
            $pagePublished = $pagePublishTbl->getEntryById($this->idpage)->current();
            if (!empty($pagePublished))
            {
                $tplId = $pagePublished->page_tpl_id;
            }
	    }
	    elseif ($this->renderMode == 'melis') 
	    {
	        /**
	         * if renderMode is melis this will try get the Page data from Saved Pages first,
	         * else the Page data will get from Published Page table
	         */
	        $pageSaved = $pageSaveTbl->getEntryById($this->idpage)->current();
	        if(!empty($pageSaved))
	        {
	            $tplId = $pageSaved->page_tpl_id;
	        }
	        else
	        {
	            $pagePublished = $pagePublishTbl->getEntryById($this->idpage)->current();
	            if (!empty($pagePublished))
	            {
	                $tplId = $pagePublished->page_tpl_id;
	            }
	        }
	    }
	    
	    // Checking if the TemplateId has value
	    if (!is_null($tplId))
	    {
	        $tpl = $tplTbl->getEntryById($tplId)->current();
	        $siteId = $tpl->tpl_site_id;
	        
	        /**
	         * Retrieving the main page of the site
	         */
	        $site = $siteTbl->getEntryById($siteId)->current();
	        if (!empty($site))
	        {
	            $mainPageId = $site->site_main_page_id;
	            /**
	             * Preparing the Page Tree using the mainPageId 
	             * in order to get the list of pages under the site
	             */
	            $menu = $this->getSubPagesRecursive($mainPageId);
	        }
	    }
	    
	    return $menu;
	}
	
	/**
	 * This method will generate listing of pages depending on the parameter specified
	 * 
	 * @param int $currentPageId, if specified the current page would be added to the list as a root
	 * @param int $subFatherPageId, if specified this will return page(s) that match to the fatherId
	 * 
	 * @return Array of MelisPageSaved/MelisPagePublished Object
	 */
	public function getSubPagesRecursive($currentPageId = null, $subFatherPageId = null)
	{
	    
	    // Table Services
	    $pageTreeTbl = $this->serviceLocator->get('MelisEngineTablePageTree');
	    $pageSrv = $this->getServiceLocator()->get('MelisEnginePage');
	    
        if (is_null($subFatherPageId))
        {
            if (!is_null($currentPageId))
            {
                /**
                 * If the currentPage has value this would be First execution of this function, 
                 * this will get from page tree the root of the Site
                 */
                $pageTree = $pageTreeTbl->getEntryById($currentPageId);
            }
        }
        else 
        {
            /**
             * After first execution $subFatherPageId param can have a value to get the children of the fatherId
             * this step can be executed if only the $subFatherPageId have a value after recursive
             */
            $pageTree = $pageTreeTbl->getPageTreeOrderedByFatherId($subFatherPageId);
        }
        
        $pages = array();
        foreach ($pageTree As $key => $val)
        {
            $pageDetails = $pageSrv->getDatasPage($val->tree_page_id);
            $pages[$key] = $pageDetails->getMelisPageTree();
            $pages[$key]->children =  $this->getSubPagesRecursive(null, $pageDetails->getId());
        }
        
        return $pages;
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