<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MelisEngine\Service\MelisEngineComposerService;
use MelisEngine\Service\MelisEngineGeneralService;
use Zend\Stdlib\ArrayUtils;

class MelisSiteConfigService extends MelisEngineGeneralService
{
    /**
     * Function to return site config by page id
     *
     * @param $pageId
     * @return array
     */
    public function getSiteConfigByPageId($pageId)
    {
        $config = array(
            'siteConfig' => array(),
            'allSites' => array(),
        );
        if(!empty($pageId)) {
            /**
             * get the language if the page
             */
            $cmsPageLang = $this->getServiceLocator()->get('MelisEngineTablePageLang');
            $pageLang = $cmsPageLang->getEntryByField('plang_page_id', $pageId)->current();
            /**
             * get page lang locale
             */
            $langData = array();
            if (!empty($pageLang)) {
                $langCmsTbl = $this->getServiceLocator()->get('MelisEngineTableCmsLang');
                $langData = $langCmsTbl->getEntryById($pageLang->plang_lang_id)->current();

            }
            /**
             * get the site config
             */
            if(!empty($langData)){
                $treeSrv = $this->getServiceLocator()->get('MelisEngineTree');
                $datasSite = $treeSrv->getSiteByPageId($pageId);
                if(!empty($datasSite->site_id)){
                    $siteId = $datasSite->site_id;
                    $siteName = $datasSite->site_name;
                    $siteConf = $this->getSiteConfig($datasSite->site_id, true);
                    if(!empty($siteConf)){
                        $config['siteConfig'] = $siteConf['site'][$siteName][$siteId][$langData->lang_cms_locale];
                        $config['siteConfig']['site_id'] = $siteId;
                        $config['allSites'] = $siteConf['site'][$siteName]['allSites'];
                    }
                }
            }
        }
        return $config;
    }

    /**
     * Returns Merged Site Config (File and DB)
     * @param $siteId
     * @param $returnAll
     * @return array
     */
    public function getSiteConfig($siteId, $returnAll = false)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('meliscms_site_tool_get_site_config_start', $arrayParameters);

        $siteId = $arrayParameters['siteId'];
        $site = $this->getSiteDataById($siteId);
        $siteName = $site['site_name'];
        $configFromFile = $this->getConfig($siteName);
        $siteConfig = [];

        if (array_key_exists('site', $configFromFile)) {
            $dbConfigData = $this->getSiteConfigFromDb($siteId);

            // merge config from file and from the db | the one on the db will be prioritized
            $siteConfig = ArrayUtils::merge($siteConfig, $configFromFile, true);

            if (!empty($dbConfigData)) {
                foreach ($dbConfigData as $dbConf) {
                    if ($dbConf['sconf_lang_id'] === '-1') {
                        $siteConfig = ArrayUtils::merge(
                            $siteConfig,
                            [
                                'site' => [
                                    $siteName => unserialize($dbConf['sconf_datas'])
                                ],
                            ],
                            true
                        );
                    } else {
                        $siteConfig = ArrayUtils::merge(
                            $siteConfig,
                            [
                                'site' => [
                                    $siteName => [
                                        $siteId => unserialize($dbConf['sconf_datas'])
                                    ],
                                ]
                            ],
                            true
                        );
                    }
                }
            }

            $activeSiteLangs = $this->getSiteActiveLanguages($siteId);

            // insert field for all languages
            foreach ($activeSiteLangs as $lang) {
                if (!array_key_exists($lang['lang_cms_locale'], $siteConfig['site'][$siteName][$siteId])) {
                    $siteConfig['site'][$siteName][$siteId][$lang['lang_cms_locale']] = [];
                }
            }

            // also merge all language config (except the general one) because some variables could be defined in one
            // one language but not on the other
            if (!empty($siteConfig['site'][$siteName][$siteId])) {
                foreach ($siteConfig['site'][$siteName][$siteId] as $langConfigKey => $langConfigVal) {
                    // merge all language config to get all possible fields
                    foreach ($siteConfig['site'][$siteName][$siteId] as $otherLangConfigKey => $otherLangConfigVal) {
                        if ($langConfigKey !== $otherLangConfigKey) {
                            $siteConfig['site'][$siteName][$siteId][$langConfigKey] = ArrayUtils::merge($siteConfig['site'][$siteName][$siteId][$langConfigKey], $otherLangConfigVal, true);
                        }
                    }

                    // override it with the current lang to preserve the correct values for the language
                    $siteConfig['site'][$siteName][$siteId][$langConfigKey] = ArrayUtils::merge($siteConfig['site'][$siteName][$siteId][$langConfigKey], $langConfigVal, true);
                }
            }

            $arrayParameters['config'] = ($arrayParameters['returnAll']) ? $siteConfig : $siteConfig['site'][$siteName][$siteId];
        } else {
            $arrayParameters['config'] = [];
        }

        $arrayParameters = $this->sendEvent('meliscms_site_tool_get_site_config_end', $arrayParameters);
        return $arrayParameters['config'];
    }

    /**
     * Returns Config
     * @param $siteName
     * @return mixed
     */
    private function getConfig($siteName)
    {
        /** @var MelisEngineComposerService $composerSrv */
        $composerSrv  = $this->getServiceLocator()->get('MelisEngineComposer');
        $config = [];

        if (!empty($composerSrv->getComposerModulePath($siteName))) {
            $modulePath = $composerSrv->getComposerModulePath($siteName);
        } else {
            $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/' . $siteName;
        }

        if (file_exists($modulePath . '/config/' . $siteName . '.config.php')) {
            $config = include $modulePath . '/config/' . $siteName . '.config.php';
        }

        return $config;
    }

    /**
     * Returns Site Config From DB
     * @param $siteId
     * @return mixed
     */
    private function getSiteConfigFromDb($siteId)
    {
        $siteConfigTable = $this->getServiceLocator()->get('MelisEngineTableCmsSiteConfig');
        return $siteConfigTable->getEntryByField('sconf_site_id', $siteId)->toArray();
    }

    /**
     * Returns Site Active Languages
     * @param $siteId
     * @return mixed
     */
    private function getSiteActiveLanguages($siteId)
    {
        $siteLangsTable = $this->getServiceLocator()->get('MelisEngineTableCmsSiteLangs');
        return $siteLangsTable->getSiteLangs(null, $siteId, null, true)->toArray();
    }

    /**
     * Returns Site Data
     * @param $siteId
     * @return mixed
     */
    private function getSiteDataById($siteId)
    {
        $siteTable = $this->getServiceLocator()->get('MelisEngineTableSite');
        return $siteTable->getEntryById($siteId)->toArray()[0];
    }
}