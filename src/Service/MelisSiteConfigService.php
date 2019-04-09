<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MelisEngine\Service\MelisEngineGeneralService;
use Zend\Stdlib\ArrayUtils;

class MelisSiteConfigService extends MelisEngineGeneralService
{
    /**
     * Returns Merged Site Config (File and DB)
     * @param $siteId
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
                            true)
                        ;
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
        }

        $arrayParameters['config'] = ($arrayParameters['returnAll']) ? $siteConfig : $siteConfig['site'][$siteName][$siteId];
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
        $moduleSrv = $this->getServiceLocator()->get('ModulesService');

        if (!empty($moduleSrv->getComposerModulePath($siteName))) {
            $modulePath = $moduleSrv->getComposerModulePath($siteName);
        } else {
            $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/' . $siteName;
        }

        $configPath = include $modulePath . '/config/' . $siteName . '.config.php';
        return $configPath;
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