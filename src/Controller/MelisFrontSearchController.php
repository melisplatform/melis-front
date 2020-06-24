<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Laminas\View\Model\ViewModel;
use MelisCore\Controller\MelisAbstractActionController;

class MelisFrontSearchController extends MelisAbstractActionController
{
    const MELIS_SITES = '/../module/MelisSites/';
    const VENDOR = '/../vendor/melisplatform/';

    /**
     * This creates lists of index with the content of every page ID that has been crawled by this function.
     * @param string moduleName - name of the site module where you can store all the indexes
     * @param int    pageid     - root page ID of the site, child pages of this ID will also be crawled.
     * @param int    expageid   - an array of page ID that you would like to exclude during the process of indexing
     * Usage:
     * Normal usage - domain.com/melissearchindex/module/Lbpam/pageid/3/exclude-pageid/0 | this will add the page and the child pages of the provided page ID
     * With page exclusions: domain.com/melissearchindex/module/Lbpam/pageid/3/exclude-pageid/12;5;20;107 | this will add the page and the child pages of the provided
     * ID page but it will exclude page ID 12, 5, 20, and 107.
     * @return ViewModel
     */
    public function addLuceneIndexAction()
    {
        $moduleName = $this->params()->fromRoute('moduleName', null);
        $pageId = $this->params()->fromRoute('pageid', null);
        $excludes = $this->params()->fromRoute('expageid', null);
        $status = '';

        /** @var \MelisEngine\Service\MelisSearchService $searchIndex */
        $searchIndex = $this->getServiceManager()->get('MelisSearch');

        if ($moduleName && $pageId) {
            $tmpexPageIds = explode(';', $excludes);
            $exPageIds = [];
            foreach ($tmpexPageIds as $id) {
                if ($id) {
                    $exPageIds[] = $id;
                }
            }

            /** Checks if the site's folder is that of MelisSite or Vendor */
            $moduleDirectory = null;
            if (is_dir($_SERVER['DOCUMENT_ROOT'] . self::MELIS_SITES . $moduleName)) {
                /** Module is located inside MelisSites folder */
                $moduleDirectory = $_SERVER['DOCUMENT_ROOT'] . self::MELIS_SITES;
            } elseif (is_dir($_SERVER['DOCUMENT_ROOT'] . self::VENDOR . $moduleName)) {
                /** Module is located inside Vendor folder */
                $moduleDirectory = $_SERVER['DOCUMENT_ROOT'] . self::VENDOR;
            }

            $status = $searchIndex->createIndex($moduleName, $pageId, $exPageIds, $moduleDirectory);
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->status = $status;

        return $view;
    }

    /**
     * @return ViewModel
     */
    public function removeLuceneIndexAction()
    {
        $moduleName = $this->params()->fromRoute('moduleName', null);
        $pageId = $this->params()->fromRoute('pageid', null);
        $excludes = $this->params()->fromRoute('expageid', null);
        $status = '';

        if ($moduleName && $pageId) {
            $tmpexPageIds = explode(';', $excludes);
            $exPageIds = [];
            foreach ($tmpexPageIds as $id) {
                if ($id) {
                    $exPageIds[] = $id;
                }
            }
            $status = array_pop($moduleName);
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->status = $status;
        return $view;
    }

    /**
     * @return ViewModel
     */
    public function optimizeIndexAction()
    {
        $moduleName = $this->params()->fromRoute('moduleName', null);
        $status = '';

        $searchIndex = $this->getServiceManager()->get('MelisSearch');

        if ($moduleName)
            $status = $searchIndex->optimizeIndex($moduleName);

        return new ViewModel([
            'status' => $status,
        ]);
    }
}
