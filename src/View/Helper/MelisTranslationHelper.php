<?php

namespace MelisFront\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use MelisEngine\Service\MelisPageService;
use MelisFront\Service\MelisTranslationService;
use Laminas\Session\Container;
use Laminas\View\Helper\AbstractHelper;

class MelisTranslationHelper extends AbstractHelper
{
	public $serviceManager;

	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
	}
	
	public function __invoke($translationKey , $locale = null)
	{
        $text = "";
	    // melis translation view helper
        /** @var MelisTranslationService $melisTrans */
        $melisTrans = $this->serviceManager->get('MelisTranslationService');
        if (! empty($locale)) {
            $text = $melisTrans->translateByLocale($translationKey,$locale);
        } else {
            // get melis back office locale
            $melisCoreLang = new Container('meliscore');
            // get translation
            $text = $melisTrans->translateByLocale($translationKey,$melisCoreLang['melis-lang-locale']);
        }

        //use other locale if translation of current locale is not given and if still no translation available in the other locale, return the tr_key
        if (empty($text) || substr(trim($text), 0, 3) == 'tr_') {             
            //get all languages available in the plaftform
            $coreLang = $this->serviceManager->get('MelisCoreTableLang');
            $languages = $coreLang->fetchAll()->toArray();
            $melisCoreLang = new Container('meliscore');
            $translatedLocale = !empty($locale) ? $locale : $melisCoreLang['melis-lang-locale'];

            foreach ($languages as $key => $langData) {
                if (trim($langData["lang_locale"]) != trim($translatedLocale)) {
                    $text = $melisTrans->translateByLocale($translationKey, $langData["lang_locale"]);   

                    //if found a non-empty translation, exit loop
                    if (!empty($text) && substr(trim($text), 0, 3) != 'tr_') {    
                        break;
                    } 
                }
            }

            //use the tr keys if no translation found
            if (empty($text) || substr(trim($text), 0, 3) == 'tr_') { 
                $text = $translationKey;
            }        	                    	
        }

        return $text;
	}

}