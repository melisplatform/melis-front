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
use Zend\Mvc\Router\Http\Segment;

/**
 * This listener will check if we are in a home page
 * to make a route for the homepage(adding locale before domain or not)
 *
 * Class MelisFrontHomePageRoutingListener
 * @package MelisFront\Listener
 */
class MelisFrontHomePageRoutingListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $callBackHandler = $events->attach(
        	MvcEvent::EVENT_ROUTE, 
        	function(MvcEvent $e){
        		$sm = $e->getApplication()->getServiceManager();

        		// AssetManager, we don't want listener to be executed if it's not a php code
        		$uri = $_SERVER['REQUEST_URI'];
        		preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
        		if (count($matches) > 1)
        		    return;

        		if (substr($uri, 0, 1) == '/')
        		    $uri = substr($uri, 1, strlen($uri));
        		
        		// Get the list of possible parameters
        		$request = $e->getRequest();
        		$getString = $request->getQuery()->toString();
        		if ($getString != '')
        		  $getString = '?' . $getString;

        		// Get the URL without the parameters to be able to compare
        		$uri = str_replace($getString, '', $uri);

    		    $domain = $_SERVER['SERVER_NAME'];
    		    $melisTableDomain = $sm->get('MelisEngineTableSiteDomain');
    		    $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain)->current();

    		    if (empty($datasDomain) || empty($uri))
    		    {
    		        return;
    		    }

                $siteId = $datasDomain->sdom_site_id;
                $uriArr = explode('/', $uri);
                /**
                 * Check if we are in home
                 */
                if(sizeof($uriArr) == 1 && !empty($uriArr[0])){
                    /**
                     * Make a locale
                     */
                    $siteLangLocale = $uriArr[0] . '_' . strtoupper($uriArr[0]);

                    $langCmsTbl = $sm->get('MelisEngineTableCmsLang');
                    $langData = $langCmsTbl->getEntryByField('lang_cms_locale', $siteLangLocale)->current();
                    /**
                     * Make sure that lang locale is exist
                     */
                    if(!empty($langData)) {
                        $langId = $langData->lang_cms_id;
                        $siteHomeTable = $sm->get('MelisEngineTableCmsSiteHome');
                        $siteHomeData = $siteHomeTable->getHomePageBySiteIdAndLangId($siteId, $langId)->current();
                        /**
                         * Check if site home page id exit from site home table,
                         * else we used the default main page id
                         * from the table site
                         */
                        if(!empty($siteHomeData)) {
                            $pageId = $siteHomeData->shome_page_id;
                        }else{
                            $siteTbl = $sm->get('MelisEngineTableSite');
                            $siteDatas = $siteTbl->getEntryById($siteId)->current();
                            $pageId = $siteDatas->site_main_page_id;
                        }
                        /**
                         * Check if page is empty
                         */
                        if (!empty($pageId)) {
                            /**
                             * get site info
                             */
                            $pageTreeService = $sm->get('MelisEngineTree');
                            $datasSite = $pageTreeService->getSiteLangUrlOptByPageId($pageId);
                            /**
                             * check if we add the language before the domain
                             */
                            if ($datasSite['siteLangOpt'] == 2) {
                                $router = $e->getApplication()->getServiceManager()->get('router');
                                // Creating dynamically the route and the params that are needed in the regular melis routing
                                $route = Segment::factory(array(
                                    'route' => $datasSite['siteLangOptVal'],
                                    'defaults' => array(
                                        'controller' => 'MelisFront\Controller\Index',
                                        'action' => 'index',
                                        'idpage' => $pageId,
                                        'renderType' => 'melis_zf2_mvc',
                                        'renderMode' => 'front',
                                        'preview' => false,
                                        'urlparams' => $getString,
                                    )
                                ));
                                // add the route to the router
                                $router->addRoute('melis-front-home-page', $route);
                            }
                        }
                    }
                }
        	},
        79);
        
        $this->listeners[] = $callBackHandler;
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