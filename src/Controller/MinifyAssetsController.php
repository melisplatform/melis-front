<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class MinifyAssetsController extends AbstractActionController
{

    /**
     * Function to minify assets
     */
    public function minifyAssetsAction ()
    {
        $result = array();
        $request = $this->getRequest();
        $siteID = $request->getPost('siteId');
        /** @var \MelisFront\Service\MinifyAssetsService $minifyAssets */
        $minifyAssets = $this->getServiceLocator()->get('MinifyAssets');
        $result = $minifyAssets->minifyAssets($siteID);

        $title = 'Compiling assets';

        /**
         * modify a little the result
         */
        $success = true;
        $message = '';
        if(!empty($result['results'][0])){
            foreach($result['results'][0] as $key => $val){
                if(!empty($val['error'])){
                    if(!$val['error']['success']){
                        $success = false;
                        $message = $val['error']['message'];
                    }
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