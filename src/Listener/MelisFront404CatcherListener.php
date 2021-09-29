<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use MelisCore\Listener\MelisGeneralListener;

/**
 * Site 404 catcher listener
 */
class MelisFront404CatcherListener extends MelisGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_FINISH, 
        	function(MvcEvent $e){        	  
                $sm = $e->getApplication()->getServiceManager();  
        		$router = $e->getRouter();         		
        		
                //get site data
                $requestUri = $router->getRequestUri();    
                $siteService = $sm->get('MelisEngineSiteService');
                $siteData = $siteService->getSiteDataByDomain($requestUri->getHost());
                $page_ext ='php';//default to php

                //get the the page_ext config values if there are any from the site config for the filtering of the page to be processed
                if(!empty($siteData)){
                    $homePageId = $siteData->site_main_page_id;                 
                    $siteConfigSrv = $sm->get('MelisSiteConfigService');
                    $page_ext_config = $siteConfigSrv->getSiteConfigByKey('page_ext', $homePageId, 'allSites');                   
              
                    if($page_ext_config){
                        $page_ext = !in_array('php', $page_ext_config)?('php|'.implode('|', $page_ext_config)):implode('|', $page_ext_config);                     
                    }
                }                
      
                //filter here the page to be processed 
                $uri = $_SERVER['REQUEST_URI'];
                preg_match('/.*\.((?!'.$page_ext.').)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
                if (count($matches) > 1)
                    return;

                $routeMatch = $router->match($sm->get('request'));
                $siteDomainTable = $sm->get('MelisEngineTableSiteDomain');

                //get params
                $params = $e->getParams();

                //if no route match or there is an error in the route, get the 301 url set in the Site Redirect Tool or if none, get the page 404 of the site
        		if(empty($routeMatch) || !empty($params['error']))
        		{           
        		   // Retrieving the router Details, This will return URL details in Object
                    $uri = $router->getRequestUri();
                    $path = $uri->getPath();
                    $queryString = $uri->getQuery();
                    $uriPath = !empty($queryString) ? ($path."?".$queryString) : $path;
                    
                    // Retrieving Site 301 if the 404 data exists as Old Url
                    $site301 = $sm->get('MelisEngineTableSite301');
                    $site301Datas = $site301->getEntryByField('s301_old_url', $uriPath)->current();
                    $url = null;
        		  
                    if(!empty($site301Datas))
                    {     
                        $url = $site301Datas->s301_new_url;                
                    }else{
        		        // check for site 404
        		        $siteDomain  = $siteDomainTable->getEntryByField('sdom_domain', $uri->getHost())->current();
        		        
        		        if(!empty($siteDomain)){
        		            
        		            $site404Table = $sm->get('MelisEngineTableSite404');
        		            $site404Data = $site404Table->getEntryByField('s404_site_id', $siteDomain->sdom_site_id)->current();
        		            
        		            if(!empty($site404Data)){
        		                
        		                $melisTree = $sm->get('MelisEngineTree');
        		                
        		                $tablePageDefaultUrls = $sm->get('MelisEngineTablePageDefaultUrls');
        		                
        		                $defaultUrls = $tablePageDefaultUrls->getEntryById($site404Data->s404_page_id);
        		                
        		                $link = '';
        		                if (!empty($defaultUrls))
        		                {
        		                    $defaultUrls = $defaultUrls->toArray();
        		                    if (count($defaultUrls) > 0)
        		                    {
        		                        $link = $defaultUrls[0]['purl_page_url'];
        		                    }
        		                }
        		                
        		                // if nothing found in DB, then let's generate
        		                if ($link == '')
        		                {
        		                    // Generate real one
        		                
        		                    // Check for SEO URL first
        		                    $idPage = $site404Data->s404_page_id;
        		                    $seoUrl = '';
        		                    $melisPage = $sm->get('MelisEnginePage');
        		                    $datasPageRes = $melisPage->getDatasPage($idPage);
        		                    $datasPageTreeRes = $datasPageRes->getMelisPageTree();
        		                
        		                    if ($datasPageTreeRes && !empty($datasPageTreeRes->pseo_url))
        		                    {
        		                        $seoUrl = $datasPageTreeRes->pseo_url;
        		                        if (substr($seoUrl, 0, 1) != '/')
        		                            $seoUrl = '/' . $seoUrl;
        		                    }
        		                
        		                    if ($seoUrl == '')
        		                    {
        		                        // First let's see if page is the homepage one ( / no id following for url)
        		                        $datasSite = $melisTree->getSiteByPageId($idPage);
        		                        if (!empty($datasSite) && $datasSite->site_main_page_id == $idPage)
        		                        {
        		                            $seoUrl = '/';
        		                        }
        		                        else
        		                        {
        		                            // if not, construct a classic Melis URL /..../..../id/xx
        		                            $datasPage = $melisTree->getPageBreadcrumb($idPage);
        		                
        		                            $seoUrl = '/';
        		                            foreach ($datasPage as $page)
        		                            {
        		                                if (!empty($datasSite) && $datasSite->site_main_page_id == $page->page_id)
        		                                    continue;
        		                                            $namePage = $page->page_name;
        		                                            $seoUrl .= $namePage . '/';
        		                            }
        		                            $seoUrl .= 'id/' . $idPage;
        		                        }
        		                    }
        		                
        		                    $link = $melisTree->cleanLink($seoUrl);
        		                }
        		                
        		                $host = $melisTree->getDomainByPageId($site404Data->s404_page_id);
        		                $url = $host . $link;
        		            }
        		        }
        		    }
        		    
        		    if($url){
        		    
        		        // Redirection
        		        $response = $e->getResponse();
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Location', $url));
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Cache-Control', 'no-cache, no-store, must-revalidate'));
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Pragma', 'no-cache'));
        		        $response->setHeaders($response->getHeaders ()->addHeaderLine('Expires', false));
        		        $response->setStatusCode(301);
        		        $response->sendHeaders();
        		        exit ();
        		    }
        		}
        	},
        -1000);
        
        $this->listeners[] = $callBackHandler;
    }
}