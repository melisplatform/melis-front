<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2018 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;


use Composer\Composer;
use MelisCore\Service\MelisCoreModulesService;
use MelisEngine\Service\MelisEngineComposerService;
use MelisEngine\Service\MelisEngineGeneralService;

class MelisSiteTranslationService extends MelisEngineGeneralService
{

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * Function to delete translation
     *
     * @param array $data - consisting the id of both key and text
     * @return mixed
     */
    public function deleteTranslation($data = array())
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_start', $arrayParameters);

        $db = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');//get db adapter
        $con = $db->getDriver()->getConnection();//get db driver connection
        $con->beginTransaction();//begin transaction
        try {
            $this->deleteTranslationKeyById($data['mst_id']);
            $this->deleteTranslationTextById($data['mstt_id']);
            $arrayParameters['results'] = true;
            $con->commit();
        } catch (\Exception $ex) {
            $con->rollback();
            $arrayParameters['results'] = false;
        }

        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_end', $arrayParameters);
        return $arrayParameters['results'];
    }

    /***
     * Function to delete key
     *
     * @param null $id
     * @return mixed
     */
    public function deleteTranslationKeyById($id = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_key_start', $arrayParameters);

        $mstTable = $this->getServiceLocator()->get('MelisSiteTranslationTable');
        $res = $mstTable->deleteById($arrayParameters['id']);

        $arrayParameters['results'] = $res;
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_key_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to delete text
     *
     * @param null $id
     * @return mixed
     */
    public function deleteTranslationTextById($id = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_text_start', $arrayParameters);

        $msttTable = $this->getServiceLocator()->get('MelisSiteTranslationTextTable');
        $res = $msttTable->deleteById($arrayParameters['id']);

        $arrayParameters['results'] = $res;
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_text_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * @param $mstId
     * @return mixed
     */
    public function deleteTranslationTextByMstId($mstId)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_text_by_mst_id_start', $arrayParameters);

        $msttTable = $this->getServiceLocator()->get('MelisSiteTranslationTextTable');
        $res = $msttTable->deleteByField('mstt_mst_id', $arrayParameters['mstId']);

        $arrayParameters['results'] = $res;
        $arrayParameters = $this->sendEvent('melis_site_translation_delete_translation_text_by_mst_id_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to save translation
     *
     * @param array $transData
     * @param array $transToDelete
     * @return mixed
     */
    public function saveTranslation($transData = array(), $transToDelete = array())
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_save_translation_start', $arrayParameters);

        $db = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');//get db adapter
        $con = $db->getDriver()->getConnection();//get db driver connection
        $con->beginTransaction();//begin transaction
        try {
            /**
             * we need to loop the translation
             * data to insert/update record
             * per language
             */
            foreach($transData as $langId => $data) {
                //check whether we insert or update the record by checking the value of id
                if ($data['mst_id'] != 0) {
                    $mstRes = $this->saveTranslationKey($data['mst_data'], $data['mst_id']);
                    if ($mstRes) {
                        $msttRes = $this->saveTranslationText($data['mstt_data'], $data['mstt_id']);
                        if ($msttRes) {
                            $arrayParameters['results'] = true;
                        }
                    } else {
                        $arrayParameters['results'] = false;
                    }
                } else {
                    $mstRes = $this->saveTranslationKey($data['mst_data']);
                    if ($mstRes) {
                        $data['mstt_data']['mstt_mst_id'] = $mstRes;
                        $msttRes = $this->saveTranslationText($data['mstt_data']);
                        if ($msttRes) {
                            $arrayParameters['results'] = true;
                        }
                    } else {
                        $arrayParameters['results'] = false;
                    }
                }
            }

            /**
             * check if there are some translation need to delete
             */
            if(!empty($transToDelete)){
                foreach($transToDelete as $key => $data){
                    $this->deleteTranslation($data);
                }
            }

            $con->commit();
            $success = true;
            $message = 'Success';
        } catch (\Exception $ex) {
            $con->rollback();
            $success = false;
            $message = $ex->getMessage();
        }
        $arrayParameters['results'] = array(
            'success' => $success,
            'message' => $message,
        );
        $arrayParameters = $this->sendEvent('melis_site_translation_save_translation_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to save translation key
     *
     * @param array $data
     * @param null $id
     * @return mixed
     */
    public function saveTranslationKey($data = array(), $id = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters['results'] = null;
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_save_translation_key_start', $arrayParameters);

        $mstTable = $this->getServiceLocator()->get('MelisSiteTranslationTable');

        if (!is_null($data) && !empty($data) && sizeof($data) > 0) {
            //check whether we update or we insert the record
            if (!is_null($id) && !empty($id) && $id != 0) {
                $mstRes = $mstTable->save($arrayParameters['data'], $id);
            } else {
                $mstRes = $mstTable->save($arrayParameters['data']);
            }

            if ($mstRes) {
                $arrayParameters['results'] = $mstRes;
            } else {
                $arrayParameters['results'] = false;
            }
        }
        $arrayParameters = $this->sendEvent('melis_site_translation_save_translation_key_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to save the translation text
     *
     * @param array $data
     * @param null $id
     * @return mixed
     */
    public function saveTranslationText($data = array(), $id = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters['results'] = null;
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_save_translation_text_start', $arrayParameters);
        $msttTable = $this->getServiceLocator()->get('MelisSiteTranslationTextTable');

        if (!is_null($data) && !empty($data) && sizeof($data) > 0) {
            //check whether we update or we insert the record
            if (!is_null($id) && !empty($id) && $id != 0) {
                $msttRes = $msttTable->save($arrayParameters['data'], $id);
            } else {
                $msttRes = $msttTable->save($arrayParameters['data']);
            }

            if ($msttRes) {
                $arrayParameters['results'] = $msttRes;
            } else {
                $arrayParameters['results'] = false;
            }
        }
        $arrayParameters = $this->sendEvent('melis_site_translation_save_translation_text_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to get the translated text by key
     *
     * @param String $translationKey
     * @param int $langId
     * @param int $siteId
     * @return mixed|null
     */
    public function getText($translationKey, $langId, $siteId)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        //check if $translationKey is not empty
        $arrayParameters['results'] = $arrayParameters['translationKey'];
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_by_key_start', $arrayParameters);
        if (!is_null($arrayParameters['langId']) && !empty($arrayParameters['langId'])) {
            //get the data
            $getAllTransMsg = $this->getSiteTranslation($arrayParameters['translationKey'], $arrayParameters['langId'], $arrayParameters['siteId']);
            if ($getAllTransMsg) {
                //get the translated text
                foreach ($getAllTransMsg as $transKey => $transMsg) {
                    if ($arrayParameters['translationKey'] == $transMsg['mst_key']) {
                        $arrayParameters['results'] = $transMsg['mstt_text'];
                        break;
                    }
                }
            }
        }
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_by_key_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to get all translated text in the file and in the db
     *
     * @param null $langId
     * @param null $translationKey - if provided, it will get only the translated text by key
     * @param int $siteId
     * @return array
     */
    public function getSiteTranslation($translationKey = null, $langId = null, $siteId = 0)
    {
        try {
            // Event parameters prepare
            $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
            // Sending service start event
            $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_list_start', $arrayParameters);
            /**
             * Get the translation from the database
             */
            $transFromDb = $this->getSiteTranslationFromDb($arrayParameters['translationKey'], $arrayParameters['langId'], $arrayParameters['siteId']);
            /**
             *  Get all the translation from the file in every module
             */
            $transFromFile = $this->getSiteTranslationFromFile($arrayParameters['translationKey'], $arrayParameters['langId'], $arrayParameters['siteId']);
            /**
             * Check if the translation from the file are already existed in the db
             * if it exist, don't include the translation from the file - the translation from db is the priority
             */
            if ($transFromDb) {
                foreach ($transFromFile as $keyFile => $keyValue) {
                    foreach ($transFromDb as $keyFromDb => $valFromDb) {
                        //if the trans key from the file already exist in the db, don't include it
                        if ($valFromDb['mst_key'] == $keyValue['mst_key'] && $valFromDb['mstt_lang_id'] == $keyValue['mstt_lang_id']) {
                            //transfer the trans file module name to db trans file module name
                            $transFromDb[$keyFromDb]['module'] = $keyValue['module'];
                            unset($transFromFile[$keyFile]);
                        }
                    }
                }
            }

            //merge all the translations
            $translationData = array_merge($transFromDb, $transFromFile);
            $translationData = array_values(array_unique($translationData, SORT_REGULAR));

            $arrayParameters['results'] = $translationData;

            $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_list_end', $arrayParameters);
        } catch (\Exception $ex) {
            $arrayParameters['results'] = array();
        }
        return $arrayParameters['results'];
    }

    /**
     * Function to get all translation from db
     *
     * @param null $langId
     * @param null $translationKey
     * @param int $siteId
     * @return array
     */
    public function getSiteTranslationFromDb($translationKey = null, $langId = null, $siteId = 0)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_db_start', $arrayParameters);

        $transFromDb = array();
        $mstTable = $this->getServiceLocator()->get('MelisSiteTranslationTable');
        $translationFromDb = $mstTable->getSiteTranslation($arrayParameters['translationKey'], $arrayParameters['langId'], $arrayParameters['siteId'])->toArray();

        foreach ($translationFromDb as $keyDb => $valueDb) {
            array_push($transFromDb, array('mst_id' => $valueDb['mst_id'], 'mstt_id' => $valueDb['mstt_id'], 'mst_site_id' => $valueDb['mst_site_id'], 'mstt_lang_id' => $valueDb['mstt_lang_id'], 'mst_key' => $valueDb['mst_key'], 'mstt_text' => $valueDb['mstt_text'], 'module' => null));
        }
        $arrayParameters['results'] = $transFromDb;
        $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_list_from_db_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Function to get all translation of every module in the file
     *
     * @param null $langId
     * @param $translationKey
     * @param $siteId
     * @return array
     */
    public function getSiteTranslationFromFile($translationKey = null, $langId = null, $siteId = 0)
    {

        $transFromFile = array();
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_list_from_file_start', $arrayParameters);

        $modules = $this->getSitesModules();

        $moduleFolders = array();
        if (!empty($arrayParameters['siteId'])) {
            $siteTbl = $this->getServiceLocator()->get('MelisEngineTableSite');
            $siteData = $siteTbl->getEntryById($arrayParameters['siteId'])->current();
            if (!empty($siteData)) {
                $siteName = $siteData->site_name;

                //check if site is came from the vendor
                $moduleSrv = $this->getServiceLocator()->get('MelisEngineComposer');
                if(!empty($moduleSrv->getComposerModulePath($siteName))){
                    $modulePath = $moduleSrv->getComposerModulePath($siteName);
                }else {
                    $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/' . $siteName;
                }

                if (is_dir($modulePath)) {
                    array_push($moduleFolders, array('path' => $modulePath, 'module' => $siteName));
                }
            }
        } else {
            foreach ($modules as $module) {
                //get path for each site
                $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/' . $module;
                if (is_dir($modulePath)) {
                    array_push($moduleFolders, array('path' => $modulePath, 'module' => $module));
                }
            }
            if(!empty($this->getSiteTranslationsFromVendor())){
                $moduleFolders = array_merge($moduleFolders, $this->getSiteTranslationsFromVendor());
            }
        }

        $transFiles = array();
        $tmpTrans = array();

        $langTable = $this->getServiceLocator()->get('MelisEngineTableCmsLang');
        /**
         * if langId is null or empty, get all the languages
         */
        if (is_null($arrayParameters['langId']) && empty($arrayParameters['langId'])) {
            if(!empty($arrayParameters['siteId'])) {
                //get the site language list
                $sitelangsTable = $this->getServiceLocator()->get('MelisEngineTableCmsSiteLangs');
                $siteLangs = $sitelangsTable->getSiteLanguagesBySiteId($arrayParameters['siteId'])->toArray();
                $langList = [];
                foreach ($siteLangs as $key => $data) {
                    $cmsLang = $langTable->getEntryById($data['slang_lang_id'])->toArray();
                    foreach ($cmsLang as $k => $lang) {
                        array_push($langList, $lang);
                    }
                }
            }else{
                $langList = $langTable->fetchAll()->toArray();
            }
        } else {
            $langList = $langTable->getEntryById($arrayParameters['langId'])->toArray();
        }

        //get the language info
        foreach ($langList as $loc) {
            /**
             * we need to concat the lang id and the lang locale to use it later
             * so that we don't need to query again to get the lang id to make it a key of the array
             * we just need to explode it to separate the id from the locale
             */
            $langStr = $loc['lang_cms_id'] . '-' . $loc['lang_cms_locale'];
            array_push($transFiles, $langStr);
        }

        //get the translation from each module
        set_time_limit(0);
        foreach ($moduleFolders as $module) {
            //check if language folder is exist
            if (file_exists($module['path'] . '/language')) {
                //loop through each filename
                foreach ($transFiles as $file) {
                    //explode the file to separate the langId from the file name
                    $file_info = explode("-", $file);
                    $langLocale = $file_info[1];
                    $langId = $file_info[0];
                    //get all translation from language folder that contains language locale.
                    $files = glob($module['path'] . '/language/*' . $langLocale . '*.php');
                    foreach ($files as $f) {
                        //check if translation file exist
                        if (file_exists($f)) {
                            //get the contents of the translation file
                            array_push($tmpTrans, array($langId => array('translations' => include($f), 'module' => $module['module'])));
                        }
                    }
                }
            }
        }

        //process/format the translations
        if ($tmpTrans) {
            foreach ($tmpTrans as $tmpIdx => $transKey) {
                //loop again to get the translation from langId
                foreach ($transKey as $langId => $value) {
                    //loop to get the key and the text
                    foreach ($value['translations'] as $k => $val) {
                        //check if key is not null to retrieve only the translation with equal to the key
                        if (!is_null($arrayParameters['translationKey']) && !empty($arrayParameters['translationKey'])) {
                            if ($k == $arrayParameters['translationKey']) {
                                array_push($transFromFile, array('mst_id' => 0, 'mstt_id' => 0, 'mst_site_id' => $siteId, 'mstt_lang_id' => $langId, 'mst_key' => $k, 'mstt_text' => $val, 'module' => $value['module']));
                            }
                        } else {//return everything
                            array_push($transFromFile, array('mst_id' => 0, 'mstt_id' => 0, 'mst_site_id' => $siteId, 'mstt_lang_id' => $langId, 'mst_key' => $k, 'mstt_text' => $val, 'module' => $value['module']));
                        }
                    }
                }
            }
        }

        $arrayParameters['results'] = $transFromFile;
        $arrayParameters = $this->sendEvent('melis_site_translation_get_trans_list_from_file_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Get the translation from the site inside
     * the vendor
     *
     * @return array
     */
    public function getSiteTranslationsFromVendor()
    {
        /** @var MelisEngineComposerService $composerSrv */
        $composerSrv = $this->getServiceLocator()->get('MelisEngineComposer');
        $vendordModules = $composerSrv->getVendorModules();

        $moduleFolders = array();
        foreach ($vendordModules as $key => $module){
            //check if module is site
            if($composerSrv->isSiteModule($module)){
                //get the full path of the site module
                $path = $composerSrv->getComposerModulePath($module);
                array_push($moduleFolders, array('path' => $path, 'module' => $module));
            }
        }
        return $moduleFolders;
    }

    /** ======================================================================================================================= **/
    /** ======================================================================================================================= **/
    /** ======================================================================================================================= **/
    /** ================================================= GET ALL SITES MODULES ================================================= **/
    /** ======================================================================================================================= **/
    /** ======================================================================================================================= **/

    /** ======================================================================================================================= **/


    private function getSitesModules()
    {
        $userModules = $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites';

        $modules = array();
        if ($this->checkDir($userModules)) {
            $modules = $this->getDir($userModules);
        }

        return $modules;
    }

    /**
     * This will check if directory exists and it's a valid directory
     * @param $dir
     * @return bool
     */
    protected function checkDir($dir)
    {
        if (file_exists($dir) && is_dir($dir) && is_writable($dir)) {
            return true;
        }

        return false;
    }

    /**
     * Returns all the sub-folders in the provided path
     * @param String $dir
     * @param array $excludeSubFolders
     * @return array
     */
    protected function getDir($dir, $excludeSubFolders = array())
    {
        $directories = array();
        if (file_exists($dir) && is_writable($dir)) {
            $excludeDir = array_merge(array('.', '..', '.gitignore'), $excludeSubFolders);
            $directory = array_diff(scandir($dir), $excludeDir);

            foreach ($directory as $d) {
                if (is_dir($dir . '/' . $d)) {
                    $directories[] = $d;
                }
            }

        }

        return $directories;
    }
}
