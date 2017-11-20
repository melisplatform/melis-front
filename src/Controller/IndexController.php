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
class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        die;
    }
    
    public function phprendererAction()
    { 
		    	
    	$idPage = $this->params()->fromRoute('idpage');
    	$renderMode = $this->params()->fromRoute('renderMode');
    	$templatePath = $this->params()->fromRoute('templatePath');
    	$pageLangId = $this->params()->fromRoute('pageLangId');
    	$pageLangLocale = $this->params()->fromRoute('pageLangLocale');

    	if ($renderMode == 'melis')
    		$getmode = 'saved';
    	else
    		$getmode = 'published';
    	
    	$melisPage = $this->getServiceLocator()->get('MelisEnginePage');
    	$datasPage = $melisPage->getDatasPage($idPage, $getmode);
    	
   // 	$melisRender = new MelisRender($sl);
   // 	list($error, $htmlContent) = $melisRender->renderPhpTemplate($idPage, $pageLangId, $pageLangLocale, $renderMode);
    	
    	$view = new ViewModel();
    	$view->setTerminal(true);
   // 	$view->setVariable('melisRenderObject', $melisRender);
    	$view->setVariable('idPage', $idPage);
    	$view->setVariable('renderMode', $renderMode);
    //	$view->setVariable('templatePath', $templatePath);
    	$view->setVariable('pageLangId', $pageLangId);
    	$view->setVariable('pageLangLocale', $pageLangLocale);
    	$view->setVariable('datasPage', $datasPage);
    	
    	return $view;
    }
    public function getIndexDataAction()
    {
        $success = 0 ;
        $data    = array();

        if($this->request()->isPost()){
            $melisFront = $this->getServiceLocator()->get("MelisFrontService");
            $data       = $melisFront->indexData();
        }

        return $data;
    }

    public function getBackOfficeCssAction()
    {
        $content  =  null;
        $response = $this->getResponse();

        $response->getHeaders()
            ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->addHeaderLine('Pragma'       , 'no-cache')
            ->addHeaderLine('Content-Type' , 'text/css;charset=UTF-8');

        $response->setContent($content);



        $view = new ViewModel();
        $view->setTerminal(true);

        $view->content = $response->getContent();

        return $view;
    }

}
