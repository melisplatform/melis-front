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

        	    // AssetManager, we don't want listener to be executed if it's not a php code
        	    $uri = $_SERVER['REQUEST_URI'];
        	    preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
        	    if (count($matches) > 1)
        	        return;
        	    
        		$sm = $e->getApplication()->getServiceManager();
        		$router = $e->getRouter();
        		
        		$sm = $e->getApplication()->getServiceManager();
        		$routeMatch = $router->match($sm->get('request'));
        		$siteDomainTable = $sm->get('MelisEngineTableSiteDomain');
        		
        		if (empty($routeMatch))
        		{
        		    // Retrieving the router Details, This will return URL details in Object
        		    $uri = $router->getRequestUri();
        		    
        		    // Retrieving Site 301 if the 404 data is exist as Old Url
        		    $site301 = $sm->get('MelisEngineTableSite301');
        		    $site301Datas = $site301->getEntryByField('s301_old_url', $uri->getPath());
        		    $url = null;
        		    if (!empty($site301Datas->count())) {
        		        
        		        $newUrlHost = array();
        		        
        		        foreach ($site301Datas as $site301Data) {
        		            
        		                if (!empty( $site301Data->s301_site_id)) {
        		                    $siteDomain  = $siteDomainTable->getEntryByField('sdom_site_id', $site301Data->s301_site_id)->current();
        		                     
        		                    if ($siteDomain->sdom_domain == $uri->getHost() && $site301Data->s301_site_id == $siteDomain->sdom_site_id) {
        		                        $url = $site301Data->s301_new_url;
        		                    }
        		                } else {
        		                    $url = $site301Data->s301_new_url;
        		                }
        		        }
        		    } else {

        		        // check for site 404
        		        $siteDomain  = $siteDomainTable->getEntryByField('sdom_domain', $uri->getHost())->current();
                               		        
        		        if (!empty($siteDomain)) {
                            
                            //check for the locale in the url
                            $uriArr = explode('/', $uri->getPath());
                            if ($uriArr) {
                                $locale = $uriArr[1];
                                /**
                                 * Make a locale
                                 */
                                $siteLangLocale = $locale . '_' . strtoupper($locale);
                                $langSrv = $sm->get('MelisEngineLang');
                                $langData = $langSrv->getLangDataByLangLocale($siteLangLocale);

                                if (!empty($langData)) {
                                    $langId = $langData->lang_cms_id;

                                    //check if there is a 404 page for a specific language
                                    $siteService = $sm->get('MelisEngineSiteService');
                                    $site404Data = $siteService->get404PageBySiteIdAndLangId($siteDomain->sdom_site_id, $langId);
                                }                               
                            }
                            
                            //if no 404 data for a specific locale or no locale in the url, get the first 404 page given the site id
                            if (!isset($site404Data)) {                              
                                $site404Table = $sm->get('MelisEngineTableSite404');
                                $site404Data = $site404Table->getEntryByField('s404_site_id', $siteDomain->sdom_site_id)->current();
                            }
        		                    		                  		            
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