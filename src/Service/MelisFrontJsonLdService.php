<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisFront\Service;

use MelisCore\Service\MelisGeneralService;

/**
 * Builds the schema.org JSON-LD graph for a published front page.
 *
 * Out of the box it emits WebSite + WebPage + BreadcrumbList from the page tree
 * and its SEO row. Modules (news, blog, commerce) extend the graph through the
 * `melis_jsonld_build` event instead of touching this service.
 */
class MelisFrontJsonLdService extends MelisGeneralService
{
    /**
     * @param int    $idPage  published page id
     * @param string $baseUrl scheme://host of the current request
     * @param string|null $locale request locale fallback (e.g. fr_FR)
     * @return array schema.org @graph (list of typed nodes); empty when the page is unknown
     */
    public function buildGraph($idPage, $baseUrl, $locale = null)
    {
        $idPage = (int) $idPage;
        if (!$idPage) {
            return [];
        }

        $sm   = $this->getServiceManager();
        $tree = $sm->get('MelisEngineTree');
        $data = $sm->get('MelisEnginePage')->getDatasPage($idPage, 'published');
        $row  = $data ? $data->getMelisPageTree() : null;
        if (empty($row) || empty($row->page_id)) {
            return [];
        }

        $baseUrl = rtrim((string) $baseUrl, '/');
        $pageUrl = $this->absolute($tree->getPageLink($idPage, true), $baseUrl);

        $name = trim((string) ($row->pseo_meta_title ?? ''));
        if ($name === '') {
            $name = trim((string) ($row->page_name ?? ''));
        }
        $desc = trim((string) ($row->pseo_meta_description ?? ''));

        // Breadcrumb comes back current -> root; reverse to root -> current.
        $crumbs = $tree->getPageBreadcrumb($idPage, 1, true);
        $crumbs = is_array($crumbs) ? array_values(array_reverse($crumbs)) : [];

        $siteName = !empty($crumbs) ? trim((string) ($crumbs[0]->page_name ?? '')) : '';
        if ($siteName === '') {
            $siteName = parse_url($baseUrl, PHP_URL_HOST) ?: $baseUrl;
        }

        $website = [
            '@type' => 'WebSite',
            '@id'   => $baseUrl . '/#website',
            'url'   => $baseUrl . '/',
            'name'  => $siteName,
        ];
        $webpage = [
            '@type'    => 'WebPage',
            '@id'      => $pageUrl . '#webpage',
            'url'      => $pageUrl,
            'name'     => $name !== '' ? $name : $siteName,
            'isPartOf' => ['@id' => $baseUrl . '/#website'],
        ];
        if ($desc !== '') {
            $webpage['description'] = $desc;
        }
        // Prefer the page's own language (joined row), fall back to the request locale.
        $lang = trim((string) ($row->lang_cms_locale ?? '')) ?: (string) $locale;
        if ($lang !== '') {
            $webpage['inLanguage'] = str_replace('_', '-', $lang);
        }

        $graph = [$website, $webpage];

        if (count($crumbs) > 1) {
            $items = [];
            $position = 1;
            foreach ($crumbs as $crumb) {
                if (empty($crumb->page_id)) {
                    continue;
                }
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => trim((string) ($crumb->page_name ?? '')),
                    'item'     => $this->absolute($tree->getPageLink((int) $crumb->page_id, true), $baseUrl),
                ];
            }
            if (!empty($items)) {
                $graph[] = [
                    '@type'           => 'BreadcrumbList',
                    '@id'             => $pageUrl . '#breadcrumb',
                    'itemListElement' => $items,
                ];
            }
        }

        // Extensibility seam: news/blog/commerce add Article/Product/... here.
        $eventParams = $this->sendEvent('melis_jsonld_build', [
            'idPage'  => $idPage,
            'row'     => $row,
            'baseUrl' => $baseUrl,
            'graph'   => $graph,
        ]);

        return (!empty($eventParams['graph']) && is_array($eventParams['graph'])) ? $eventParams['graph'] : $graph;
    }

    /**
     * @param array $graph nodes from buildGraph()
     * @return string the <script type="application/ld+json"> tag, or '' when empty
     */
    public function renderScript(array $graph)
    {
        if (empty($graph)) {
            return '';
        }
        $doc  = ['@context' => 'https://schema.org', '@graph' => array_values($graph)];
        // JSON_HEX_TAG escapes < and > so a value can never break out of the <script> element.
        $json = json_encode($doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
        if ($json === false) {
            return '';
        }
        return '<script type="application/ld+json">' . $json . '</script>';
    }

    private function absolute($link, $baseUrl)
    {
        $link = (string) $link;
        if ($link === '') {
            return $baseUrl . '/';
        }
        if (preg_match('#^https?://#i', $link)) {
            return $link;
        }
        return $baseUrl . '/' . ltrim($link, '/');
    }
}
