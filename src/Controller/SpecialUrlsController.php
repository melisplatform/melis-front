<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\FeedModel;

class SpecialUrlsController extends AbstractActionController
{
    public function sitemapAction()
    {
        $siteMainPage = 0;
        $menu = array();
        
        $domain = $_SERVER['SERVER_NAME'];
        $melisTableDomain = $this->getServiceLocator()->get('MelisEngineTableSiteDomain');
        $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain);
        if (!empty($datasDomain) || !empty($datasDomain->current()))
        {
            $siteDomain = $datasDomain->current();
            $siteId = $siteDomain->sdom_site_id;

            $melisTableSite = $this->getServiceLocator()->get('MelisEngineTableSite');
            $datasSite = $melisTableSite->getEntryById($siteId);
            if (!empty($datasSite) || !empty($datasSite->current()))
            {
                $site = $datasSite->current();
                $siteMainPage = $site->site_main_page_id;
                
                $menu = array();
                $navigation = new \MelisFront\Navigation\MelisFrontNavigation($this->getServiceLocator(),
                    $siteMainPage, 'front');

                $melisPage = $this->getServiceLocator()->get('MelisEnginePage');
		        $datasPageRes = $melisPage->getDatasPage($siteMainPage);
		        if (!empty($datasPageRes))
		        {
		            $datasPageRes = $datasPageRes->getMelisPageTree()->getArrayCopy();
		            $menu[] = $navigation->formatPageInArray($datasPageRes);
		        }
		        
                $menuTmp = $navigation->getChildrenRecursive($siteMainPage);
                $menuTmp = $this->getAllPagesInOneArray(array(), $menuTmp);
                
                $menu = array_merge($menu, $menuTmp);
            }
        }
        
        $view = new FeedModel();
        $view->siteMainPage = $siteMainPage;
        $view->domain = $domain;
        $view->menu = $menu;
        
        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'text/xml'));
        
        return $view;
    }
    private function removeAllPagesInOneArray($mainArray, $subArray)
    {
        foreach ($subArray as $itemSubArray)
        {
            $itemToPush = $itemSubArray;
            if (!empty($itemSubArray['pages']))
                unset($itemToPush['pages']);
            array_pop($mainArray);


        }

        return $mainArray;
    }
    
    private function getAllPagesInOneArray($mainArray, $subArray)
    {
        foreach ($subArray as $itemSubArray)
        {
            $itemToPush = $itemSubArray;
            if (!empty($itemSubArray['pages']))
               unset($itemToPush['pages']);
            array_push($mainArray, $itemToPush);
            
            if (!empty($itemSubArray['pages']))
                $mainArray = $this->getAllPagesInOneArray($mainArray, $itemSubArray['pages']);
            
        }
        
        return $mainArray;
    }

}
