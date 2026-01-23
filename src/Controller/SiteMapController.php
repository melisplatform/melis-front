<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Laminas\Db\Sql\Select;
use Laminas\View\Model\ViewModel;
use MelisCore\Controller\MelisAbstractActionController;

class SiteMapController extends MelisAbstractActionController
{
    public function sitemapAction()
    {
        $siteMainPage = 0;
        $menu = array();

        $domain = $_SERVER['SERVER_NAME'];
        $melisTableDomain = $this->getServiceManager()->get('MelisEngineTableSiteDomain');
        $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain);
        if (!empty($datasDomain) || !empty($datasDomain->current())) {
            $siteDomain = $datasDomain->current();
            $siteId = $siteDomain->sdom_site_id;

            $melisTableSite = $this->getServiceManager()->get('MelisEngineTableSite');
            $datasSite = $melisTableSite->getEntryById($siteId);
            if (!empty($datasSite) || !empty($datasSite->current())) {
                $site = $datasSite->current();
                $siteMainPage = $site->site_main_page_id;

                $menu = array();
                $navigation = new \MelisFront\Navigation\MelisFrontNavigation(
                    $this->getServiceManager(),
                    $siteMainPage,
                    'front'
                );

                $melisPage = $this->getServiceManager()->get('MelisEnginePage');
                $datasPageRes = $melisPage->getDatasPage($siteMainPage);
                if (!empty($datasPageRes)) {
                    $datasPageRes = $datasPageRes->getMelisPageTree()->getArrayCopy();
                    $menu[] = $navigation->formatPageInArray($datasPageRes);
                }

                $menuTmp = $navigation->getAllSubpages($siteMainPage);
                $menuTmp = $this->getAllPagesInOneArray(array(), $menuTmp);

                $menu = array_merge($menu, $menuTmp);
            }
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->siteMainPage = $siteMainPage;
        $view->domain = $domain;
        $view->menu = $menu;

        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'text/xml'));

        return $view;
    }

    public function siteMapIndexAction()
    {
        $siteLangsData = [];
        $domain = $_SERVER['SERVER_NAME'];

        $melisTableDomain = $this->getServiceManager()->get('MelisEngineTableSiteDomain');
        $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain);

        $site = $datasDomain->current();
        if (!empty($site)) {

            $siteLangsTbl = $this->getServiceManager()->get('MelisEngineTableCmsSiteLangs');
            $siteLangs = $siteLangsTbl->getEntryByField('slang_site_id', $site->sdom_site_id)->toArray();

            $langTbl = $this->getServiceManager()->get('MelisEngineTableCmsLang');

            foreach ($siteLangs as $siteLang) {
                $lang = $langTbl->getEntryById($siteLang['slang_lang_id'])->current();
                if ($lang) {
                    $siteLangsData[] = $site->sdom_scheme . '://' . $site->sdom_domain . '/' . explode('_', $lang->lang_cms_locale)[0] . '/sitemap.xml';
                }
            }

            // sort alphabetically 
            sort($siteLangsData);
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->siteLangsData = $siteLangsData;

        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'text/xml'));

        return $view;
    }

    public function siteMapLangPagesAction()
    {
        $siteMainPage = 0;
        $menu = array();

        $domain = $_SERVER['SERVER_NAME'];

        $lang = $this->params()->fromRoute('lang');

        $melisTableDomain = $this->getServiceManager()->get('MelisEngineTableSiteDomain');
        $datasDomain = $melisTableDomain->getEntryByField('sdom_domain', $domain);
        if (!empty($datasDomain) || !empty($datasDomain->current())) {
            $siteDomain = $datasDomain->current();
            $siteId = $siteDomain->sdom_site_id;

            $melisTableSite = $this->getServiceManager()->get('MelisEngineTableSite');
            $datasSite = $melisTableSite->getEntryById($siteId);
            if (!empty($datasSite) || !empty($datasSite->current())) {

                $site = $datasSite->current();
                // $siteMainPage = $site->site_main_page_id;

                /** @var MelisSiteConfigService $siteConfigSrv */
                $siteConfigSrv = $this->serviceManager->get('MelisSiteConfigService');
                $siteMainPage = $siteConfigSrv->getSiteConfigByKey('homePageId', $site->site_main_page_id,  'sites',  $lang);

                if ($siteMainPage) {
                    $menu = array();
                    $navigation = new \MelisFront\Navigation\MelisFrontNavigation(
                        $this->getServiceManager(),
                        $siteMainPage,
                        'front'
                    );

                    $melisPage = $this->getServiceManager()->get('MelisEnginePage');
                    $datasPageRes = $melisPage->getDatasPage($siteMainPage);
                    if (!empty($datasPageRes)) {
                        $datasPageRes = $datasPageRes->getMelisPageTree()->getArrayCopy();
                        $menu[] = $navigation->formatPageInArray($datasPageRes);
                    }

                    $menuTmp = $navigation->getAllSubpages($siteMainPage);
                    $menuTmp = $this->getAllPagesInOneArray(array(), $menuTmp);

                    $menu = array_merge($menu, $menuTmp);
                }
            }
        }

        $pageIds = [];
        foreach ($menu as $data)
            $pageIds[] = $data['idPage'];

        $pageLangs = [];

        if ($pageIds) {
            $pageLangTbl = $this->getServiceManager()->get('MelisEngineTablePageLang');
            $tblGw = $pageLangTbl->getTableGateway();
            // $dbAdaptor = $tblGw->getAdapter();

            $_select = $tblGw->getSql()->select();
            $_select->columns(['plang_page_id_initial']);
            $_select->where->in('plang_page_id', $pageIds);
            // $pageLangInitials = $tblGw->selectWith($select)->toArray();


            $select = $tblGw->getSql()->select();
            $select->columns(['plang_page_id', 'plang_page_id_initial']);

            $select->join(
                ['p' => 'melis_cms_page_published'],
                'p.page_id = melis_cms_page_lang.plang_page_id',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['l' => 'melis_cms_lang'],
                'l.lang_cms_id = melis_cms_page_lang.plang_lang_id',
                ['lang_cms_locale'],
                Select::JOIN_LEFT
            );

            $select->where(['p.page_status' => 1]);
            $select->where->in('plang_page_id_initial', $_select);

            $results = $tblGw->selectWith($select)->toArray();
            foreach ($results as $res) {

                if (!isset($pageLangs[$res['plang_page_id_initial']]))
                    $pageLangs[$res['plang_page_id_initial']] = [];

                $pageLangs[$res['plang_page_id_initial']][$res['plang_page_id']] = $res;
            }
        }


        $view = new ViewModel();
        $view->setTerminal(true);

        $view->siteMainPage = $siteMainPage;
        $view->domain = $domain;
        $view->menu = $menu;
        $view->pageLangs = $pageLangs;

        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'application/xml; charset=utf-8'));

        return $view;
    }

    private function getAllPagesInOneArray($mainArray, $subArray)
    {
        foreach ($subArray as $itemSubArray) {
            $itemToPush = $itemSubArray;
            $pageStat   = $itemSubArray['pageStat'] ?? null;

            if ($pageStat) {
                if (!empty($itemSubArray['pages']))
                    unset($itemToPush['pages']);
                array_push($mainArray, $itemToPush);
            }
            if (!empty($itemSubArray['pages']))
                $mainArray = $this->getAllPagesInOneArray($mainArray, $itemSubArray['pages']);
        }

        return $mainArray;
    }
}
