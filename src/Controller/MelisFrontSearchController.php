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

class MelisFrontSearchController extends AbstractActionController
{

    /**
     * This creates lists of index with the content of every page ID that has been crawled by this function.
     * @param string moduleName - name of the site module where you can store all the indexes
     * @param int    pageid     - root page ID of the site, child pages of this ID will also be crawled.
     * @param int    expageid   - an array of page ID that you would like to exclude during the process of indexing
     * Usage: 
     * Normal usage - domain.com/melissearchindex/module/Lbpam/pageid/3/exclude-pageid/0 | this will add the page and the child pages of the provided page ID
     * With page exclusions: domain.com/melissearchindex/module/Lbpam/pageid/3/exclude-pageid/12;5;20;107 | this will add the page and the child pages of the provided 
     * ID page but it will exclude page ID 12, 5, 20, and 107.
     */
    public function addLuceneIndexAction()
    {
        $moduleName = $this->params()->fromRoute('moduleName', null);
        $pageId     = $this->params()->fromRoute('pageid', null);
        $excludes   = $this->params()->fromRoute('expageid', null);
        $status = '';
        
        $searchIndex = $this->getServiceLocator()->get('MelisSearch');
        
        if($moduleName && $pageId) {
            $tmpexPageIds = explode(';', $excludes);
            $exPageIds = array();
            foreach($tmpexPageIds as $id) {
                if($id) {
                    $exPageIds[] = $id;
                }
            }
            
            $status = $searchIndex->createIndex($moduleName, $pageId, $exPageIds);
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->status = $status;
        return $view;
    }

    public function optimizeIndexAction()
    {
        $moduleName = $this->params()->fromRoute('moduleName', null);
        $status = '';
        
        $searchIndex = $this->getServiceLocator()->get('MelisSearch');
        
        if($moduleName)
            $status = $searchIndex->optimizeIndex($moduleName);
        
        return new ViewModel(array(
            'status'  => $status,
        ));
    }
    
}