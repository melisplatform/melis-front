<?php

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;

/**
 * SECURITY (audit FE-H1) : frontière d'identité du MODE ÉDITION du rendu front.
 *
 * `renderMode=melis` et `/preview` rendent la version `saved` (BROUILLON) d'une page — un contenu
 * réservé aux rédacteurs. Jusqu'ici AUCUN test d'identité n'existait sur ce chemin dans
 * melis-front : l'anonyme n'était bloqué que par accident (fatal du forward layout vers MelisCms
 * non chargé en mode front — vérifié live : 500 ; et 302 login de melis-core::checkIdentity
 * uniquement quand `?melisSite=` force le chargement des modules back-office). Un site qui charge
 * MelisCms dans son module.load.php front perdait la première barrière.
 *
 * Ce listener rend la frontière DÉLIBÉRÉE et locale : toute requête web dont le routeMatch porte
 * renderMode=melis ou preview exige une identité back-office. Flux légitimes intacts : l'iframe
 * d'édition legacy et le rendu interne du canvas React portent la session BO ; le CLI est exempt.
 */
class MelisFrontEditionModeIdentityListener implements ListenerAggregateInterface
{
    public $listeners = [];

    public function attach(EventManagerInterface $events, $priority = -40)
    {
        // Après le routage (il faut le routeMatch), avant le dispatch : une réponse retournée
        // depuis EVENT_ROUTE court-circuite le dispatch — le brouillon n'est jamais construit.
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onRoute(MvcEvent $e)
    {
        if (php_sapi_name() === 'cli') {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        if (empty($routeMatch)) {
            return;
        }

        $renderMode = $routeMatch->getParam('renderMode', 'front');
        $preview    = $routeMatch->getParam('preview', false);
        if ($renderMode !== 'melis' && empty($preview)) {
            return; // rendu front publié : public par design
        }

        $hasIdentity = false;
        try {
            $hasIdentity = $e->getApplication()->getServiceManager()
                ->get('MelisCoreAuth')->hasIdentity();
        } catch (\Throwable $ex) {
            // service d'auth indisponible → refus (fail-closed)
        }
        if ($hasIdentity) {
            return;
        }

        // Mêmes conventions ET même mécanique que melis-core::checkIdentity (header + exit) :
        // retourner une Response depuis EVENT_ROUTE ne suffit pas ici, les listeners de RENDU de
        // melis-front (MelisFrontLayoutListener → forward) s'exécuteraient quand même sur la
        // réponse court-circuitée et fataleraient sans contrôleur dispatché.
        $request = $e->getRequest();
        $xhr     = $request->getHeaders()->get('X-Requested-With');
        $isXhr   = $xhr && strtolower((string) $xhr->getFieldValue()) === 'xmlhttprequest';

        $e->stopPropagation(true);

        if ($isXhr || strtoupper((string) $request->getMethod()) !== 'GET') {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        header('Location: /melis/login');
        exit;
    }
}
