<?php

namespace MelisFront\View\Helper;

use MelisEngine\Service\MelisPageService;
use MelisFront\Service\MelisTranslationService;
use Laminas\Session\Container;
use Laminas\View\Helper\AbstractHelper;

class MelisTranslationHelper extends AbstractHelper
{
	public $serviceManager;

	public function __construct($sm)
	{
		$this->serviceManager = $sm;
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

        return $text;
	}

}