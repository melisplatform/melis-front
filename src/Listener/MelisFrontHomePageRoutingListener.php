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
use Laminas\Mvc\Router\Http\Segment;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener will check if we are in a home page
 * to make a route for the homepage(adding locale before domain or not)
 *
 * Class MelisFrontHomePageRoutingListener
 * @package MelisFront\Listener
 */
class MelisFrontHomePageRoutingListener extends MelisGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
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
                $domainSrv = $sm->get('MelisEngineSiteDomainService');
                $datasDomain = $domainSrv->getDomainByDomainName($domain);

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

                    $langSrv = $sm->get('MelisEngineLang');
                    $langData = $langSrv->getLangDataByLangLocale($siteLangLocale);
                    /**
                     * Make sure that lang locale is exist
                     */
                    $siteService = $sm->get('MelisEngineSiteService');
                    if(!empty($langData)) {
                        $langId = $langData->lang_cms_id;
                        $siteHomeData = $siteService->getHomePageBySiteIdAndLangId($siteId, $langId);
                        /**
                         * Check if site home page id exit from site home table,
                         * else we used the default main page id
                         * from the table site
                         */
                        if(!empty($siteHomeData)) {
                            $pageId = $siteHomeData->shome_page_id;
                        }else{
                            $pageId = $siteService->getSiteMainHomePageIdBySiteId($siteId);
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
}