<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 * 
 */

namespace MelisFront\Controller;

use Laminas\Mvc\MvcEvent;
use MelisCore\Controller\MelisAbstractActionController;

/**
 * This class extends the MelisAbstractActionController and is to be used
 * for any controller on any MelisSite.
 *
 */
class MelisSiteActionController extends MelisAbstractActionController
{
	// Default variables to use everywhere
	public $idPage;
	public $renderType;
	public $renderMode;
	
	public $pageLangId;
	public $pageLangLocale;
	
	public $oMelisTree;
	public $oMelisPage;
	
	public function onDispatch(MvcEvent $event)
	{

		$this->idPage = $this->params()->fromRoute('idpage');
		$this->renderType = $this->params()->fromRoute('renderType');
		if (empty($this->renderType))
			$this->renderType = 'regular_zf2_mvc';
		$this->renderMode = $this->params()->fromRoute('renderMode');
		if (empty($this->renderMode))
			$this->renderMode = 'front';

		$this->pageLangId = $this->params()->fromRoute('pageLangId');
		$this->pageLangLocale = $this->params()->fromRoute('pageLangLocale');
		$this->oMelisPage = $this->params()->fromRoute('datasPage');
		
		$pageBreadCrumb = null;
		$mainPageId = null;
		$datasPage = null;
		
		if (!empty($this->idPage))
		{
			$melisEnginePage = $this->getServiceManager()->get('MelisEnginePage');
			$melisEngineTree = $this->getServiceManager()->get('MelisEngineTree');
			$melisEngineTableSite = $this->getServiceManager()->get('MelisEngineTableSite');
			
			// Get Current Pages Breadcrumb
			$pageBreadCrumb = $melisEngineTree->getPageBreadcrumb($this->idPage);
			
			// Get Current Page Data
			$pageTemplate = $this->oMelisPage->getMelisTemplate();
			
			$idSite = null;
			if (!empty($pageTemplate)){
				// Get Current Page Site ID by looking at Current Page Template
				$idSite = $pageTemplate->tpl_site_id;
			}
			
			if ($idSite!=null){
				// Get Page Id by using Site ID that found on Current Page Template
				$sitePageId = $melisEngineTableSite->getSiteById($idSite, getenv('MELIS_PLATFORM'))->current();
				if (!empty($sitePageId)){
					$mainPageId = $sitePageId->site_main_page_id;
				}
			}
		
		// Site language for site translations
			$melisEnginLangService = $this->getServiceManager()->get('MelisEngineLang');
			$siteLang = $melisEnginLangService->getSiteLanguage();
			$siteLangId = $siteLang['langId'];
			$siteLangLocale = $siteLang['langLocale'];

			$this->layout()->setVariables(array(
				'idPage' => $this->idPage,
				'renderType' => $this->renderType,
				'renderMode' => $this->renderMode,
				'pageLangId' => $this->pageLangId,
				'pageLangLocale' => $this->pageLangLocale,
				'pageBreadCrumb' => $pageBreadCrumb,
				'mainPageId' => $mainPageId,
				'pageTemplate' => $pageTemplate,
				'siteLangId' => $siteLangId,
				'siteLangLocale' => $siteLangLocale,
				'preview'    => $this->params()->fromRoute('preview')
			));
	
			$this->oMelisPage = $datasPage;
		}

		return parent::onDispatch($event);
	}

	public function trackDataOnDispatch()
	{
		$this->idPage = $this->params()->fromRoute('idpage');
		$this->renderType = $this->params()->fromRoute('renderType');
		$data = array();

		if (!empty($this->idPage))
		{
			$melisEnginePage = $this->getServiceManager()->get('MelisEnginePage');
			$melisEngineTree = $this->getServiceManager()->get('MelisEngineTree');
			$melisEngineTableSite = $this->getServiceManager()->get('MelisEngineTableSite');


			$this->layout()->setVariables(array(
				'idPage' => $this->idPage,
				'renderType' => $this->renderType,
				'renderMode' => $this->renderMode,
				'pageLangId' => $this->pageLangId,
				'pageLangLocale' => $this->pageLangLocale,
				'pageBreadCrumb' => $pageBreadCrumb,
				'mainPageId' => $mainPageId
			));

			$this->oMelisPage = $datasPage;
		}
		return $data['result'];

	}
}
