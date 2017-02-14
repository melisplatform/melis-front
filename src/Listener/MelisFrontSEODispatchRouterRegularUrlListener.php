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
use Zend\Session\Container;

/**
 * This listener check if the Melis Page URL is correct.
 * If not, it will try to generate and redirect the correct one.
 * If ok, it will change the router to dispatch in the correct site module
 * and will add a few datas in the route to be used in the site.
 * 
 * Before redirecting in a module site, an event melisfront_site_dispatch_ready is launched, 
 * allowing other listener of plugins to update/modify the result of redirections, 404 or 
 * even the router's datas for dispatching in the site module.
 * Using this event is the correct way to interact with SEO.
 * 
 */
class MelisFrontSEODispatchRouterRegularUrlListener 
    extends MelisFrontSEODispatchRouterAbstractListener
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

                // If no id of page, then we're not in a melis page
                if (!empty($params['idpage']))
                { 
                   $idpage = $params['idpage'];
        	       $renderType = $routeMatch->getParam('renderType');
        	       $renderMode = $routeMatch->getParam('renderMode');
        	       
        	       // Creating the result array that will also be sent as a paramater when event is triggered
                   $routingResult = $routeMatch->getParams();
                   $routingResult['301'] = null;
                   $routingResult['301_type'] = '';
                   $routingResult['404'] = null;
                   $routingResult['front_route'] = $matchedRouteName_full;

                   // Getting the datas of the page depending on front or BO
    	           if ($renderMode == 'melis')
    	               $getmode = 'saved';
    	           else
    	               $getmode = 'published';
    	               
                   // Get page datas
                   $melisPage = $sm->get('MelisEnginePage');
                   $datasPage = $melisPage->getDatasPage($idpage, $getmode);
                   $datasTemplate = $datasPage->getMelisTemplate();
                   $pageTree = $datasPage->getMelisPageTree();
                   
                   // Save the datas of the page in the results object so other listeners can
                   // have access without making a query when updating the results
                   $routingResult['datasPage'] = $datasPage;
                    
                   // Check if page exist and page published
                   if (empty($pageTree) || ($pageTree->page_status == 0 && $renderMode == 'front'))
                   {
                       if (isset($pageTree->page_status) && $pageTree->page_status == 0 && 
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
                       if ($renderMode == 'front')
                       {
                           $type = $datasPage->getType();
                           $status = $pageTree->page_status;
                           if (($type == 'published' && $status == 0) || $type == 'saved')
                               $routingResult['404'] = $this->redirect404($e, $idpage);
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
                       if (!empty($datasPage->getMelisPageTree()))
                       {
                           $routingResult['pageLangId'] = $datasPage->getMelisPageTree()->plang_lang_id;
                           $routingResult['pageLangLocale'] = $datasPage->getMelisPageTree()->lang_cms_locale;
                           
                           $container = new Container('melisplugins');
                           $container['melis-plugins-lang-id'] = $datasPage->getMelisPageTree()->plang_lang_id;
                           $container['melis-plugins-lang-locale'] = $datasPage->getMelisPageTree()->lang_cms_locale;
                            
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
                   }
                   
                   /**
                    * SENDING EVENT BEFORE MAKING REDIRECTIONS
                    * OTHER PLUGINS SHOULD ATTACH THIS EVENT
                    */
                   $routingResult = $eventManager->prepareArgs($routingResult);
                   $eventManager->trigger('melisfront_site_dispatch_ready', $this, $routingResult);
                   
                   // Changing the router with what we have in the routing array generated
                   if ($routingResult['301'] != null || $routingResult['404'] != null)
                   {
                       // Code fo redirection
                       if ($routingResult['301'] != null)
                           $statusCode = 301;
                       if ($routingResult['404'] != null)
                           $statusCode = 404;
                       
                       // Exceptional case, 404 completely undefined, even the cross site default one in DB
                       if ($routingResult['404'] === '')
                           die('404');
                           
                       // Redirection    
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
                           // Setting the template and creating translations and session for the site module
                           $e->getViewModel()->setTemplate($datasPage->getMelisTemplate()->tpl_zf2_website_folder . '/' . $datasTemplate->tpl_zf2_layout);
                           $this->createTranslations($e, $datasPage->getMelisTemplate()->tpl_zf2_website_folder,
                                                     $datasPage->getMelisPageTree()->lang_cms_locale);
                           $this->initSession($datasPage->getMelisTemplate()->tpl_zf2_website_folder);
                       }
                       
                       // Making a route param of every variable in the result object
                       foreach ($routingResult as $keyResult => $result)
                           $routeMatch->setParam($keyResult, $result);
                   }
            	   
                }
            },
        100);
        
        $this->listeners[] = $callBackHandler;
    }
    
    /**
     * This function handles the basic SEO 301 for Melis pages.
     * This will be used when a page is unpublished
     * 
     * @param MvcEvent $e
     * @param int $idpage
     * @return string|NULL The URL of the 301 page defined in SEO
     */
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
    
    /**
     * This function handles the basic SEO Redirection for Melis pages.
     * This will occur if the redirect field for the page has been filled with a link.
     * It will happen no matter the page is online or offline.
     * 
     * @param MvcEvent $e
     * @param int $idpage
     * @return string|NULL The URL of the page to be redirected to
     */
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