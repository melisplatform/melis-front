<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

/**
 * Injects schema.org JSON-LD into the <head> of a rendered front page.
 *
 * Runs on EVENT_FINISH (after the site template produced the HTML) so no site
 * template has to opt in. Non-front and non-HTML responses are skipped.
 */
class MelisFrontJsonLdListener implements ListenerAggregateInterface
{
    public $listeners = [];

    public function attach(EventManagerInterface $events, $priority = 50)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $e) {
            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch) {
                return;
            }
            $params = $routeMatch->getParams();
            if (($params['renderMode'] ?? '') !== 'front' || empty($params['idpage'])) {
                return;
            }

            $response = $e->getResponse();
            if (!method_exists($response, 'getContent')) {
                return;
            }
            $content = $response->getContent();
            if (!is_string($content) || stripos($content, '</head>') === false) {
                return;
            }
            // A site template may already emit its own JSON-LD; never double up.
            if (stripos($content, 'application/ld+json') !== false) {
                return;
            }

            $sm      = $e->getApplication()->getServiceManager();
            $request = $sm->get('Request');
            $uri     = method_exists($request, 'getUri') ? $request->getUri() : null;
            $host    = $uri ? $uri->getHost() : ($_SERVER['HTTP_HOST'] ?? '');
            if (!$host) {
                return;
            }
            $scheme  = ($uri && $uri->getScheme()) ? $uri->getScheme() : 'http';
            $baseUrl = $scheme . '://' . $host;

            $container = new Container('melisfront');
            $locale    = $container['melis-lang-locale'] ?? null;

            try {
                $svc    = $sm->get('MelisFrontJsonLdService');
                $script = $svc->renderScript($svc->buildGraph((int) $params['idpage'], $baseUrl, $locale));
            } catch (\Throwable $ex) {
                error_log('MELIS_JSONLD ' . __METHOD__ . ': ' . $ex->getMessage());
                return;
            }
            if ($script === '') {
                return;
            }

            $pos = stripos($content, '</head>');
            $response->setContent(substr($content, 0, $pos) . $script . substr($content, $pos));
        }, $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}
