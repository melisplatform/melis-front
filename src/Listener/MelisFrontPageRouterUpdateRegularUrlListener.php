<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * This listener will activate when a page is deleted
 * 
 */
class MelisFrontPageRouterUpdateRegularUrlListener 
    extends MelisFrontPageRouterUpdateAbstractListener
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_DISPATCH, 
        	function(MvcEvent $e){
                
        	    $sm = $e->getApplication()->getServiceManager();
        	    $eventManager = $e->getApplication()->getEventManager();
        	    
        	    $routeMatch = $e->getRouteMatch();
        	    $matchedRouteName_full = $routeMatch->getMatchedRouteName();
        	    
        	    $routeNameParts = explode('/', $matchedRouteName_full);
        	    if (!empty($routeNameParts))
        	        $matchedRouteName = $routeNameParts[0];
        	        
                $idpage = null;
                $params = $routeMatch->getParams();

                if (!empty($params['idpage']))
                { 
                   $idpage = $params['idpage'];
        	       $renderType = $routeMatch->getParam('renderType');
        	       $renderMode = $routeMatch->getParam('renderMode');
                   $routingResult = $routeMatch->getParams();
                   $routingResult['301'] = null;
                   $routingResult['301_type'] = '';
                   $routingResult['404'] = null;
                   $routingResult['front_route'] = $matchedRouteName_full;

            //	   if ($matchedRouteName == 'melis-front' || $matchedRouteName == 'melis-front-page-seo')
            //	   {
        	           if ($renderMode == 'melis')
        	               $getmode = 'saved';
        	           else
        	               $getmode = 'published';
        	               
	                   // Get page datas
	                   $melisPage = $sm->get('MelisEnginePage');
	                   $datasPage = $melisPage->getDatasPage($idpage, $getmode);
	                   $datasTemplate = $datasPage->getMelisTemplate();
	                   $pageTree = $datasPage->getMelisPageTree();
	                   
	                   $routingResult['datasPage'] = $datasPage;
	                    
	                   // Check if page exist and page published
	                   if (empty($pageTree) || ($pageTree->page_status == 0 && $renderMode == 'front'))
	                   {
	                       if (!empty($pageTree->page_status) && $pageTree->page_status == 0 && 
	                           $renderMode == 'front')
	                       {
	                           // The page is inactive, let's try SEO redirection
	                           $routingResult['301'] = $this->redirectPageSEO301($e, $idpage);
	                           if ($routingResult['301'] != null)
	                               $routingResult['301_type'] = 'seo301';
	                       }

	                       // If no SEO has been found, then this will end up as 404
	                       if ($routingResult['301'] == null)
	                           $routingResult['404'] = $this->redirect404($e, $idpage);
	                   }
	                   else
	                   {
	                       if ($renderMode == 'front')
	                       {
	                           $type = $datasPage->getType();
	                           $status = $pageTree->page_status;
	                           if (($type == 'published' && $status == 0) || $type == 'saved')
	                               $routingResult['404'] = $this->redirect404($e, $idpage);
	                       }
	                   }

	                   // If displayed in front, check URLs, Redirect 301 if URL not good
	                   if ($renderMode != 'melis' && $routingResult['301'] == null && $routingResult['404'] == null)
	                   {
                           // Try SEO Url first
                           $routingResult['301'] = $this->redirectPageSEORedirect($e, $idpage);
                           if ($routingResult['301'] != null)
                               $routingResult['301_type'] = 'seoURL';
                       
                           // No SEO, then try regular Page Url
                           if ($routingResult['301'] == null)
                           {
                               $routingResult['301'] = $this->redirectPageMelisURL($e, $idpage);
                               if ($routingResult['301'] != null)
                                   $routingResult['301_type'] = 'seoMelisURL';
                           }
	                   }
	                   
	                   // Setting all router datas
	                   if ($routingResult['301'] == null && $routingResult['404'] == null)
	                   {
	                       $routingResult['pageLangId'] = $datasPage->getMelisPageTree()->plang_lang_id;
	                       $routingResult['pageLangLocale'] = $datasPage->getMelisPageTree()->lang_cms_locale;
	                       
	                       if (!empty($datasTemplate))
	                       {
	                           if ($datasTemplate->tpl_type == 'ZF2')
	                           {
	                               $routingResult['module'] = $datasTemplate->tpl_zf2_website_folder;
	                               $routingResult['controller'] = $datasTemplate->tpl_zf2_website_folder . '\Controller\\' . $datasTemplate->tpl_zf2_controller;
	                               $routingResult['action'] = $datasTemplate->tpl_zf2_action;
	                           }
	                           else
	                               if ($datasTemplate->tpl_type == 'PHP')
	                               {
	                                   $routingResult['action'] = 'phprenderer';
	                                   $routingResult['renderType'] = 'melis_php';
	                               }
	                       }
	                   }
	                   
	                   
	                   // Sending Event
	                   // Other special urls can catch this event and modify before the routing object is changed
	                   $routingResult = $eventManager->prepareArgs($routingResult);
	                   $eventManager->trigger('melisfront_site_dispatch_ready', $this, $routingResult);
	                   
	               /*    echo '<pre>';
	                   print_r($routingResult);
	                   echo '</pre>'; */
	                  // die;
	                  
	                   // Changing the router with what we have in the routing array generated
	                   if ($routingResult['301'] != null || $routingResult['404'] != null)
	                   {
	                       if ($routingResult['301'] != null)
	                           $statusCode = 301;
	                       if ($routingResult['404'] != null)
	                           $statusCode = 404;
	                       
	                       // Exceptional case, 404 completely undefined, even the cross site default one in DB
	                       if ($routingResult['404'] === '')
	                           die('404');
	                           
	                       $response = $e->getResponse ();
	                       $response->setHeaders($response->getHeaders ()->addHeaderLine('Location', $routingResult[''.$statusCode]));
	                       $response->setStatusCode($statusCode);
	                       $response->sendHeaders();
	                       exit ();
	                   }
	                   else 
	                   {
	                       // We're all good to display a page!
	                       unset($routingResult['301']);
	                       unset($routingResult['404']);
	                       $datasPage = $routingResult['datasPage'];
	                       
	                       if (!empty($datasPage->getMelisTemplate()))
	                       {
	                           $e->getViewModel()->setTemplate($datasPage->getMelisTemplate()->tpl_zf2_website_folder . '/' . $datasTemplate->tpl_zf2_layout);
	                           $this->createTranslations($e, $datasPage->getMelisTemplate()->tpl_zf2_website_folder,
	                                                     $datasPage->getMelisPageTree()->lang_cms_locale);
	                           $this->initSession($datasPage->getMelisTemplate()->tpl_zf2_website_folder);
	                       }
	                       
	                       
	                 //      unset($routingResult['datasPage']);
	                       
	                       foreach ($routingResult as $keyResult => $result)
	                           $routeMatch->setParam($keyResult, $result);
	                       
	                   }
	                   
            	//   }
            	   
                }
            },
        100);
        
        $this->listeners[] = $callBackHandler;
    }
    
    public function redirectPageSEO301($e, $idpage)
    {
    	$sm = $e->getApplication()->getServiceManager();
        $router = $e->getRouter();
        $uri = $router->getRequestUri();
         
        // Check for defined SEO 301 redirection
        $uri = $uri->getPath();
        if (substr($uri, 0, 1) == '/')
            $uri = substr($uri, 1, strlen($uri));
        $melisTablePageSeo = $sm->get('MelisEngineTablePageSeo');
        $datasPageSeo = $melisTablePageSeo->getEntryById($idpage);
        if (!empty($datasPageSeo))
        {
            $datasPageSeo = $datasPageSeo->current();
            if (!empty($datasPageSeo) && !empty($datasPageSeo->pseo_url_301))
            {
                if (substr($datasPageSeo->pseo_url_301, 0, 4) != 'http')
                    $newuri = '/' . $datasPageSeo->pseo_url_301;
                else
                    $newuri = $datasPageSeo->pseo_url_301;

                $newuri .= $this->getQueryParameters($e);
                    
                return $newuri;
            }
        }
        
        return null;
    }
    
    public function redirectPageSEORedirect($e, $idpage)
    {
    	$sm = $e->getApplication()->getServiceManager();
    	
    	$router = $e->getRouter();
    	$uri = $router->getRequestUri();
    	 
    	// Check for defined SEO regular redirection
    	$uri = $uri->getPath();
    	if (substr($uri, 0, 1) == '/')
    		$uri = substr($uri, 1, strlen($uri));
    			
    	$melisTablePageSeo = $sm->get('MelisEngineTablePageSeo');
    	$datasPageSeo = $melisTablePageSeo->getEntryById($idpage);
    	if (!empty($datasPageSeo))
    	{
    		$datasPageSeo = $datasPageSeo->current();
    		if (!empty($datasPageSeo) && !empty($datasPageSeo->pseo_url_redirect))
    		{
    			if (substr($datasPageSeo->pseo_url_redirect, 0, 4) != 'http')
    				$newuri = '/' . $datasPageSeo->pseo_url_redirect;
    			else
    				$newuri = $datasPageSeo->pseo_url_redirect;

    			$newuri .= $this->getQueryParameters($e);
    				
    		    return $newuri;
    		}
    	}
    
    	return null;
    }
}