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
     * Function to the site config by key
     *
     * @param $key
     * @param string $section
     * @param null $language
     * @return array|string
     */
    public function getSiteConfigByKey($key, $section = 'sites', $language = null)
    {
        /**
         * get the page id via the request
         */
        $router = $this->serviceLocator->get('router');
        $request = $this->serviceLocator->get('request');
        $routeMatch = $router->match($request);
        $params = $routeMatch->getParams();

        try {

            if(empty($section))
                $section = 'sites';

            if ($section == 'sites' || $section == 'allSites') {
                $siteConfigData = $this->getSiteConfigByPageId($params['idpage']);
                if ($section == 'sites') {
                    if (empty($language)) {
                        return $siteConfigData['siteConfig'][$key];
                    } else {
                        $langLocale = strtolower($language) . '_' . strtoupper($language);
                        $siteConfigData = $this->getSiteConfigByPageId($params['idpage'], $langLocale);
                        return $siteConfigData['siteConfig'][$key];
                    }
                } else {
                    return $siteConfigData['allSites'][$key];
                }
            } else {
                $siteConfigData = $this->getSiteConfig($section);
                $data = [];
                foreach ($siteConfigData as $locale => $value) {
                    $data[$locale] = array($key => $value[$key]);
                }
                if (empty($language))
                    return $data;
                else {
                    $langLocale = strtolower($language) . '_' . strtoupper($language);
                    return $data[$langLocale][$key];
                }
            }
        }catch (\Exception $ex) {
            return null;
        }
    }

    /**
     * Function to return site config by page id
     *
     * @param $pageId
     * @param $langLocale - ex: en_EN, fr_FR
     * @return array
     */
    public function getSiteConfigByPageId($pageId, $langLocale = false)
    {
        $siteConfig = array(
            'siteConfig' => array(),
            'allSites' => array(),
        );

        /**
         * get the site config
         */
        $config = $this->serviceLocator->get('config');

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
                    if(!empty($config['site'])){
                        if($langLocale){
                            $siteConfig['siteConfig'] = $config['site'][$siteName][$siteId][$langLocale];
                        }else {
                            $siteConfig['siteConfig'] = $config['site'][$siteName][$siteId][$langData->lang_cms_locale];
                        }
                        $siteConfig['siteConfig']['site_id'] = $siteId;
                        $siteConfig['allSites'] = $config['site'][$siteName]['allSites'];
                    }
                }
            }
        }
        return $siteConfig;
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
            /**
             * Make sure that we are accessing the correct config
             */
            if(isset($siteConfig['site'][$siteName][$siteId])) {
                $activeSiteLangs = $this->getSiteActiveLanguages($siteId);

                // add langauges that are active but not on the config file
                foreach ($activeSiteLangs as $lang) {
                    if (!array_key_exists($lang['lang_cms_locale'], $siteConfig['site'][$siteName][$siteId])) {
                        $siteConfig['site'][$siteName][$siteId][$lang['lang_cms_locale']] = [];
                    }
                }

                // also merge all language config (except the general one) because some variables could be defined in one
                // one language but not on the other
                if (!empty($siteConfig['site'][$siteName][$siteId])) {
                    foreach ($siteConfig['site'][$siteName][$siteId] as $langConfigKey => $langConfigVal) {
                        foreach ($siteConfig['site'][$siteName][$siteId] as $otherLangConfigKey => $otherLangConfigVal) {
                            if ($langConfigKey !== $otherLangConfigKey) {
                                foreach ($otherLangConfigVal as $configKey => $configValue) {
                                    if (!array_key_exists($configKey, $siteConfig['site'][$siteName][$siteId][$langConfigKey])) {
                                        if (is_array($configValue)) {
                                            $arr = [];

                                            foreach ($configValue as $key => $val) {
                                                if (!is_array($val)) {
                                                    $arr[$key] = '';
                                                }
                                            }

                                            $siteConfig['site'][$siteName][$siteId][$langConfigKey] = ArrayUtils::merge($siteConfig['site'][$siteName][$siteId][$langConfigKey], [$configKey => $arr], true);
                                        } else {
                                            $siteConfig['site'][$siteName][$siteId][$langConfigKey] = ArrayUtils::merge($siteConfig['site'][$siteName][$siteId][$langConfigKey], [$configKey => ''], true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

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

                $arrayParameters['config'] = ($arrayParameters['returnAll']) ? $siteConfig : $siteConfig['site'][$siteName][$siteId];
            }else{
                $arrayParameters['config'] = [];
            }
        } else {
            $arrayParameters['config'] = [];
        }

        $arrayParameters = $this->sendEvent('meliscms_site_tool_get_site_config_end', $arrayParameters);
        return $arrayParameters['config'];
    }

    /**
     * Returns Config From File
     * @param $siteName
     * @return mixed
     */
    public function getConfig($siteName)
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