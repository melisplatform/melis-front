<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Laminas\View\Model\JsonModel;
use MelisCore\Controller\MelisAbstractActionController;

class MinifyAssetsController extends MelisAbstractActionController
{

    /**
     * Function to minify assets
     */
    public function minifyAssetsAction ()
    {
        $request = $this->getRequest();
        $siteID = $request->getPost('siteId');
        /** @var \MelisFront\Service\MinifyAssetsService $minifyAssets */
        $minifyAssets = $this->getServiceManager()->get('MinifyAssets');
        $result = $minifyAssets->minifyAssets($siteID);

        $title = 'Compiling assets';

        /**
         * modify a little the result
         */
        $success = true;
        $message = '';
        if(!empty($result['results'])){
            foreach($result['results'] as $key => $val){
                if(!$val['success']){
                    $success = false;
                    $message = $val['message'];
                }
            }
        }

        return new JsonModel(array(
            'title' => $title,
            'message' => wordwrap($message, 20, "\n", true),
            'success' => $success,
        ));
    }
}