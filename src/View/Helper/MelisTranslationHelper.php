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

        //use English as a fallback locale
        if (empty($text)) {        	
        	$text = $melisTrans->translateByLocale($translationKey,'en_EN');        	
        }

        return $text;
	}

}