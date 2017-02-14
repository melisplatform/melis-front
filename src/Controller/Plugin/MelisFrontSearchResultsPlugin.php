<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * This plugin implements the business logic of the
 * "SearchResults" plugin.
 * 
 * Please look inside app.plugins.php for possible awaited parameters
 * in front and back function calls.
 * 
 * front() and back() are the only functions to create / update.
 * front() generates the website view
 * back() generates the plugin view in template edition mode (TODO)
 * 
 * Configuration can be found in $pluginConfig / $pluginFrontConfig / $pluginBackConfig
 * Configuration is automatically merged with the parameters provided when calling the plugin.
 * Merge detects automatically from the route if rendering must be done for front or back.
 * 
 * How to call this plugin without parameters:
 * $plugin = $this->MelisFrontSearchResultsPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisFrontSearchResultsPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/search/search-results'
 * );
 * $pluginView = $plugin->render($parameters);
 * 
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'searchresults');
 * 
 * How to display in your controller's view:
 * echo $this->searchresults;
 * 
 * 
 */
class MelisFrontSearchResultsPlugin extends MelisTemplatingPlugin
{
    // the key of the configuration in the app.plugins.php
    public $configPluginKey = 'melisfront';
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        
        // Pagination
        $current = (!empty($this->pluginFrontConfig['pagination']['current'])) ? $this->pluginFrontConfig['pagination']['current'] : 1;
        $nbPerPage = (!empty($this->pluginFrontConfig['pagination']['nbPerPage'])) ? $this->pluginFrontConfig['pagination']['nbPerPage'] : 1;
        $nbPageBeforeAfter = (!empty($this->pluginFrontConfig['pagination']['nbPageBeforeAfter'])) ? $this->pluginFrontConfig['pagination']['nbPageBeforeAfter'] : 1;
        
        $pageId = (!empty($this->pluginFrontConfig['pageId'])) ? $this->pluginFrontConfig['pageId'] : null;
        $keyword = (!empty($this->pluginFrontConfig['keyword'])) ? $this->pluginFrontConfig['keyword'] : null;
        
        $moduleName = (!empty($this->pluginFrontConfig['siteModuleName'])) ? $this->pluginFrontConfig['siteModuleName'] : null;
        
        $moduleDirWritable = true;
        
        if (file_exists('module/MelisSites/'.$moduleName.'/luceneIndex/indexes')){
            $isIndex = true;
            $indexUrl = '';
        }
        else 
        {
            $isIndex = false;
            
            if (!is_writable('module/MelisSites/'.$moduleName.'/luceneIndex/'))
            {
                $moduleDirWritable = false;
            }
            else
            {
                /**
                 * Indexing Site will use the main page of the Site
                 */
                $pageTreeSrv = $this->getServiceLocator()->get('MelisEngineTree');
                $pageSite = $pageTreeSrv->getSiteByPageId($pageId);
                
                $mainPageId = 1;
                if (!is_null($pageSite))
                {
                    $mainPageId = $pageSite->site_main_page_id;
                }
                
                // Get the current server protocol
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
                
                $indexUrl = $protocol.$_SERVER['SERVER_NAME'].'/melissearchindex/module/'.$moduleName.'/pageid/'.$mainPageId.'/exclude-pageid/0';
            }
        }
        
        $searchresults = array();
        if ($isIndex && $keyword)
        {
            $searchSvc = $this->getServiceLocator()->get('MelisSearch');
            $searchresults = $searchSvc->search($keyword, $moduleName, true);
            if (!empty($searchresults))
            {
                $searchresults = (Array) simplexml_load_string($searchresults);
                $searchresults = (!empty($searchresults['result'])) ? $searchresults['result'] : array();
                
                
                // Checking if the Search array result is multidimensional array or single array
                if (is_object($searchresults))
                {
                    // Making the search result to be multidimensional array
                    $temp[] = $searchresults;
                    $searchresults = $temp;
                }
            }
        }
        
        $paginator = new Paginator(new ArrayAdapter($searchresults));
        $paginator->setCurrentPageNumber($current)
                    ->setItemCountPerPage($nbPerPage);
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'indexerOk' => $isIndex,
            'indexerURL' => $indexUrl,
            'moduleDirWritable' => $moduleDirWritable,
            'searchresults' => $paginator,
            'nbPageBeforeAfter' => $nbPageBeforeAfter
        );
        
        // return the variable array and let the view be created
        return $viewVariables;
    }
    
    /**
     * This function return the back office rendering for the template edition system
     * TODO
     */
    public function back()
    {
        return array();
    }
}
