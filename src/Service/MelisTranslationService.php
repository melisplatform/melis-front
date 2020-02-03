<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2020 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MelisAssetManager\Service\MelisModulesService;
use MelisEngine\Service\MelisEngineGeneralService;
use Zend\Session\Container;

class MelisTranslationService extends MelisEngineGeneralService
{
    /** @var MelisModulesService */
    private $moduleSvc;

    public function __construct(MelisModulesService $moduleService)
    {
        $this->moduleSvc = $moduleService;
    }

    /**
     *
     *  get all module translations by locale
     *
     * @param string $locale
     * @return array
     */
    public function getTranslationsByLocale($locale = "en_EN")
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_translation_get_trans_by_locale_start', $arrayParameters);
        $transMessages   = [];
        $tmpTrans        = [];
        $modules         =  $this->moduleSvc->getAllModules();
        $locale          = $arrayParameters['locale'];
        $moduleFolders   = [];
        // get modules path
        foreach ($modules as $module)
        {
            array_push($moduleFolders, $this->moduleSvc->getModulePath($module));
        }

        $transFiles = array(
            $locale.'.interface.php',
            $locale.'.forms.php',
        );
        $insideDirTrans = [];
        set_time_limit(0);
        foreach($moduleFolders as $module) {
            if(file_exists($module.'/language')) {
                foreach($transFiles as $file) {
                    if(file_exists($module.'/language/'.$file)) {
                        $tmpTrans[] = include($module.'/language/'.$file);
                    }
                }
                // get the directory
                $iterator = new \RecursiveDirectoryIterator($module . "/language", \RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new \RecursiveIteratorIterator($iterator,\RecursiveIteratorIterator::CHILD_FIRST);
                /** @var \SplFileInfo $file */
                // get the files under the directory
                foreach($files as $file) {
                    if (stristr($file->getBasename(),$locale)){
                        // get the translation based on locale
                        $tmpTrans[]= include $file->getFileInfo()->getPathname();
                    } else if (stristr($file->getBasename(),"en_EN")){
                        // fall back locale
                        $tmpTrans[] = include $file->getFileInfo()->getPathname();
                    }
                }

            }
        }

        if($tmpTrans) {
            foreach($tmpTrans as $tmpIdx => $transKey) {
                foreach($transKey as $key => $value) {
                    $transMessages[$key] = $value;
                }
            }
        }
        // results
        $arrayParameters['results'] = $transMessages;
        // send event
        $arrayParameters = $this->sendEvent('melis_translation_get_trans_by_locale_end', $arrayParameters);

        return $arrayParameters['results'];

    }

    /**+
     *
     *
     * get translations by key and locale
     *
     * @param $translationKey
     * @param string $locale
     * @return mixed
     */
    public function translateByLocale($translationKey, $locale = "en_EN")
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('trans_by_locale_start', $arrayParameters);
        $text = $translationKey;
        // check translation key in the translations
        $translations = $this->getTranslationsByLocale($locale);
        if (array_key_exists($translationKey, $translations)) {
            $text = $translations[$translationKey];
        }

        // results
        $arrayParameters['results'] = $text;
        // send event
        $arrayParameters = $this->sendEvent('trans_by_locale_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     *  translate translationkey based from Back-office locale
     * @param $translationKey
     * @return mixed
     */
    public function boTranslate($translationKey)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('bo_translate_start', $arrayParameters);
        $text = $translationKey;
        // get bo locale
        $melisBoContainer = new Container('meliscore');
        // check translation key in the translations
        $translations = $this->getTranslationsByLocale($melisBoContainer['melis-lang-locale']);
        if (array_key_exists($translationKey, $translations)) {
            $text = $translations[$translationKey];
        }

        // results
        $arrayParameters['results'] = $text;
        // send event
        $arrayParameters = $this->sendEvent('bo_translate_end', $arrayParameters);

        return $arrayParameters['results'];
    }
}
