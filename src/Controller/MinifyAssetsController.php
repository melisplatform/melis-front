<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Controller;

use Laminas\Authentication\Storage\Session as SessionStorage;
use Laminas\View\Model\JsonModel;
use MelisCore\Controller\MelisAbstractActionController;

class MinifyAssetsController extends MelisAbstractActionController
{

    /**
     * Function to minify assets
     */
    public function minifyAssetsAction ()
    {
        /**
         * Asset (re)compilation is a heavy back-office operation. Require a valid
         * authenticated Melis back-office session; deny anonymous requests (DoS vector).
         */
        if (!$this->isBackofficeAuthenticated()) {
            return new JsonModel([
                'title' => 'Compiling assets',
                'message' => 'Unauthorized',
                'success' => false,
            ]);
        }

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

    /**
     * True when the request carries an authenticated Melis back-office session.
     *
     * /minify-assets is a front route: MelisModuleManager only loads the site's
     * modules there, so MelisCore (and its MelisCoreAuth service) is usually absent.
     * Read the same 'Melis_Auth' session storage MelisCoreAuth uses instead.
     *
     * @return bool
     */
    private function isBackofficeAuthenticated()
    {
        $serviceManager = $this->getServiceManager();
        if ($serviceManager->has('MelisCoreAuth')) {
            return $serviceManager->get('MelisCoreAuth')->hasIdentity();
        }

        return !(new SessionStorage('Melis_Auth'))->isEmpty();
    }
}