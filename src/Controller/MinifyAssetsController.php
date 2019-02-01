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
        if($request->isPost()) {
            $siteID = $request->getPost('siteId');

            /** @var \MelisFront\Service\MinifyAssetsService $minifyAssets */
            $minifyAssets = $this->getServiceLocator()->get('MinifyAssets');
            $result = $minifyAssets->minifyAssets($siteID);
        }

        $title = 'Compiling assets for '.$result['siteName'];

        return new JsonModel(array(
            'title' => $title,
            'errors' => $result['errors'],
        ));
    }
}