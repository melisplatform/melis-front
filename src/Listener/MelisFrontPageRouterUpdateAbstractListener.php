<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Session\SessionManager;

/**
 * This listener will activate when a page is deleted
 * 
 */
abstract class MelisFrontPageRouterUpdateAbstractListener 
    implements ListenerAggregateInterface
{
    abstract public function attach(EventManagerInterface $events);

    public function redirectPageMelisURL(MvcEvent $e, $idpage)
    {
        $sm = $e->getApplication()->getServiceManager();
    
        $melisTree = $sm->get('MelisEngineTree');
        $link = $melisTree->getPageLink($idpage, true);
    
        $host = $melisTree->getDomainByPageId($idpage);
    
        $router = $e->getRouter();
        $uri = $router->getRequestUri();
    
        if ($host != '')
            $fullUrl = $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath();
        else
            $fullUrl = $uri->getPath();
    
        // Removing the optional parameters from url before checking
        $routeMatch = $e->getRouteMatch();
        $params = $routeMatch->getParams();
        if (!empty($params['urlparams']))
            $fullUrl = str_replace('/'.$params['urlparams'], '', $fullUrl);
             
        if ($fullUrl != $link)
        {
            // Adding optional parameters if there are
            if (!empty($params['urlparams']))
                $link .= '/' . $params['urlparams'];

            $link .= $this->getQueryParameters($e);    
            
            $request = $e->getRequest();
            if (!$request->isPost()) // don't rewrite url if post request or you'll loose post datas
                return $link;
        }
        
        return null;
    }
    
    public function redirect404(MvcEvent $e, $idpage = null)
    {
    	$sm = $e->getApplication()->getServiceManager();
        $eventManager = $e->getApplication()->getEventManager();
		
    	$melisTree = $sm->get('MelisEngineTree');
		$melisSiteDomain = $sm->get('MelisEngineTableSiteDomain');
		$melisSite404 = $sm->get('MelisEngineTableSite404');
    	
    	if ($idpage == null)
    	{
    		// idPage is not working, we get the site through the domain
    		// used to redirect to the right 404
    		$router = $e->getRouter();
			$uri = $router->getRequestUri();
			$domainTxt = $uri->getHost();
    	}
    	else
    	{
    		// We get the site using the idPage and redirect to the site's 404
    		$domainTxt = $melisTree->getDomainByPageId($idpage, true);
    		if (empty($domainTxt))
    		{
    		    $router = $e->getRouter();
    		    $uri = $router->getRequestUri();
    		    $domainTxt = $uri->getHost();
    		}
    		
    		$domainTxt = str_replace('http://', '', $domainTxt);
    		$domainTxt = str_replace('https://', '', $domainTxt);
    	}

    	// Get the siteId from the domain
    	if ($domainTxt)
    	{
			$domain = $melisSiteDomain->getEntryByField('sdom_domain', $domainTxt);
			
			$domain = $domain->current();
			if ($domain)
    			$siteId = $domain->sdom_site_id;
			else
			    return '';
    	}
    	else
    	    return '';

    	// Get the 404 of the siteId
    	$site404 = $melisSite404->getEntryByField('s404_site_id', $siteId);
    	if ($site404)
    	{
    		$site404 = $site404->current();
    		if (empty($site404))
    		{
    			// No entry in DB for this siteId, let's get the general one (siteId -1)
    			$site404 = $melisSite404->getEntryByField('s404_site_id', -1);
    			$site404 = $site404->current();
    		}
    	}
    	
    	if (empty($site404))
    	{
    		// No 404 found
    		return '';
    	}
    	else
    	{
    		// Check if the 404 defined exist also!
		    $melisPage = $sm->get('MelisEnginePage');
		    $datasPage = $melisPage->getDatasPage($site404->s404_page_id, 'published');
		    $pageTree = $datasPage->getMelisPageTree();
		    
		    if (empty($pageTree))
		        return ''; // The 404 page defined doesn't exist or is not published
    		
    		// Redirect to the 404 of the site
    		$link = $melisTree->getPageLink($site404->s404_page_id, true);
    		
    		return $link;
    	}
    }
    
    public function getQueryParameters($e)
    {
        $request = $e->getRequest();
        $getString = $request->getQuery()->toString();
        
        if ($getString != '')
            $getString = '?' . $getString;
        
        return $getString;
    }
    
    public function createTranslations($e, $siteFolder, $locale)
    {
    	$sm = $e->getApplication()->getServiceManager();
    	$translator = $sm->get('translator');

    	if (!empty($siteFolder) && !empty($locale))
    	   $translator->addTranslationFile(
    	       'phparray',  
    	       $_SERVER['DOCUMENT_ROOT'] . '/../modules/MelisSites/' . 
    	           $siteFolder . '/language/' . $locale . '.php'
    	   );
    }
    
    public function initSession($name)
    {
        $sessionManager = new SessionManager();
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
        $container = new Container($name);
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}