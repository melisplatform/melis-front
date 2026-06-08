---
title: MelisFront module
package: melisplatform/melis-front
doc_type: module-documentation
audience: ai
language: en
module_version: unversioned   # no `version` field in composer.json; this doc tracks the current source
last_reviewed: 2026-06-08
maintainer: Melis Technology
keywords: [front, rendering, website, page, render-pipeline, routing, seo, templating-plugin, view-helper, navigation, menu, minify, melis]
screenshots_dir: ./images
---

# MelisFront Module — Functional Documentation (for AI)

> **Purpose of this document**: describe, functionally and technically, the
> `melisplatform/melis-front` module, so that an AI (or a developer) can understand
> *what the module does*, *how the front rendering pipeline works*, *which plugins/helpers
> it provides* and *where the corresponding code lives*.
>
> **Audience**: consumed by the **MelisAI** module (a MelisPlatform module that exposes an
> MCP function to answer user questions). MelisAI fetches this `.md` file on demand.
>
> **Status**: reviewed 2026-06-08 against the current source. The module carries no
> semantic version (no `version` in `composer.json`).
>
> **Screenshots**: MelisFront renders the **public websites** themselves; it has no
> back-office tool UI. The visible back-office page-editing screens (which reuse this render
> pipeline in "melis" mode) are documented in the
> [MelisCms](../../../melis-cms/etc/MelisAI/doc/MelisCms.md) doc, so this doc has no `images/`.

---

## 0. The MelisCms / MelisFront / MelisEngine trio

These three modules are the heart of the Melis website platform and must be understood
together (full explanation in the [MelisEngine](../../../melis-engine/etc/MelisAI/doc/MelisEngine.md) doc):

- **MelisEngine** — the shared technical layer that **owns the CMS database model** (pages,
  templates, sites, languages, SEO, styles…) and exposes it via table gateways + services +
  caching, plus the `MelisTemplatingPlugin` base class.
- **MelisFront** *(this module)* — the **front-office rendering system** that turns a URL into
  a rendered HTML page for the public website.
- **MelisCms** — the **back-office** that builds/administers the sites and **reuses this
  module's render pipeline** to show a live, editable page in the editor.

**Load order**: `melis-core` → **melis-front** → **melis-engine** → **melis-cms**. MelisFront
declares only `melis-core` as a Melis dependency, but **at runtime it consumes MelisEngine's
services and table gateways** (engine is always present in a full platform, loads right after
front, and is the data layer front renders from).

---

## 1. Overview

`MelisFront` is the **website rendering engine**: it intercepts front HTTP requests, resolves
them to a CMS page (via MelisEngine), runs a **listener-based render pipeline** that builds
the final HTML (layout, SEO meta, page CSS, plugin assets, caching), and provides the
**templating plugins** and **view helpers** that site templates use to compose pages (editable
zones, menus, breadcrumbs, links, translations, site config, GDPR banner…).

| Item | Value |
|---|---|
| Package name | `melisplatform/melis-front` |
| Type | `melisplatform-module` |
| PHP namespace | `MelisFront\` → `src/` (PSR-4) |
| Melis category | `cms` |
| License | OSL-3.0 |
| PHP required | `^8.1 | ^8.3` |
| Owns DB tables? | **No** — all data comes from MelisEngine |

### Dependencies (`composer.json`)

- `melisplatform/melis-core` (`^5.2`) — base, events, rights, translations (only declared Melis dep)
- `laminas/laminas-navigation` — builds site menus/navigation from the page tree
- `laminas/laminas-serializer` — page-cache serialization
- `laminas/laminas-file` — file/asset operations
- `matthiasmullie/minify` — CSS/JS minification (asset bundles)

> At runtime MelisFront also requires **MelisEngine** (page/template/site/lang models &
> services) — see §0 and §6.

---

## 2. Routing — how a URL becomes a page

Declared in `config/module.config.php`:

- **`melis-front`** (regex `…/id/(?<idpage>[0-9]+)`) → `MelisFront\Controller\Index::index`
  with defaults `renderType: melis_zf2_mvc`, `renderMode: front`, `preview: false`. The
  controller action itself does almost nothing — **the rendering is performed by listeners**
  on the dispatch/finish events (§4).
  - child **`/renderMode/melis`** → `renderMode: melis` (the **back-office edit mode**, used by MelisCms)
  - child **`/preview`** → `renderMode: melis`, `preview: true` (preview the *saved* version)
- **special URLs** (`/`): `sitemap(.xml/.html)`, `/css/plugin-width.css` & `/css/page-plugin-width.css`
  (`StyleController`), the search indexer (`/melissearchindex/…`, `MelisFrontSearchController`),
  and a generic `/MelisFront/[:controller[/:action]]` dispatch.
- **`/melispluginrenderer`** → `MelisPluginRendererController::getPlugin` (AJAX render of a single plugin).
- **`/minify-assets`** → `MinifyAssetsController::minifyAssets` (build CSS/JS bundles).

So the **same page** can be rendered in `front` mode (public site) or `melis` mode (editable
in the BO) — this dual mode is the core link to MelisCms.

---

## 3. Controllers

- `IndexController` — the front entry point (real work happens in listeners).
- `SpecialUrlsController` — sitemap & special URLs.
- `MelisFrontSearchController` — Lucene search index build/optimize endpoints.
- `MelisPluginRendererController` — AJAX rendering of one templating plugin (used by the BO editor).
- `StyleController` — generates plugin/page width CSS.
- `MinifyAssetsController` — compiles minified CSS/JS bundles per site.

---

## 4. The render pipeline (listeners)

MelisFront wires ~19 listeners in `src/Module.php`. The pipeline is event-driven (Laminas MVC
`EVENT_DISPATCH` then `EVENT_FINISH`), not a single controller. Highlights:

**At module load (`EVENT_LOAD_MODULES_POST`)**
- `MelisFrontSEORouteListener` — builds the **SEO-friendly URL routes** from the DB before
  other modules load.
- `MelisFrontSiteConfigListener` — merges file + DB **site config**.
- `MelisFrontMiniTemplateConfigListener` — registers mini-template plugin configs.

**On dispatch (`EVENT_DISPATCH`)**
- `MelisFrontXSSParameterListener` — sanitizes query params.
- `MelisFrontHomePageRoutingListener` / `…HomePageIdOverrideListener` — route `/` to the site home page.
- `MelisFrontSEODispatchRouterRegularUrlListener` — validates the page exists, handles
  404/301, then **fires `melisfront_site_dispatch_ready`** (the hook other modules use).
- `MelisFront404To301Listener` / `MelisFront404CatcherListener` — 404 → 301 and 404 fallback.
- (BO-only) `MelisFrontPluginLangSessionUpdateListener`, `MelisFrontDeletePluginCacheListener`.

**On finish (`EVENT_FINISH`, builds the final HTML)**
- `MelisFrontPluginsToLayoutListener` — injects templating-plugin CSS/JS into the layout.
- `MelisFrontSEOMetaPageListener` — injects `<title>`, meta description, canonical from the page SEO.
- `MelisFrontAttachCssListener` — appends page-specific CSS (from engine styles).
- `MelisFrontLayoutListener` — wraps content in the **front layout** or, in `melis` mode, the
  **back-office layout + TinyMCE** (the editable overlay used by MelisCms).
- `MelisFrontPageCacheListener` — caches the rendered page (filesystem cache via engine).
- `MelisFrontMinifiedAssetsCheckerListener` — injects the minified `bundle.css` / `bundle.js`.

---

## 5. Templating plugins, view helpers & navigation

These are what **site templates** (and the BO editor) use to compose pages. All plugins
extend MelisEngine's `MelisTemplatingPlugin`.

### Templating plugins (`controller_plugins`)
- **Editable zones**: `MelisFrontTagHtmlPlugin` (WYSIWYG/TinyMCE), `MelisFrontTagTextareaPlugin`,
  `MelisFrontTagMediaPlugin` — the zones an editor fills in the BO and that render on the front.
- **Navigation**: `MelisFrontMenuPlugin` (site menus), `MelisFrontBreadcrumbPlugin`.
- **Layout/content**: `MelisFrontDragDropZonePlugin` (drop zone for plugins in the BO),
  `MelisFrontBlockSectionPlugin`, `MelisFrontGenericContentPlugin`,
  `MelisFrontShowListFromFolderPlugin` (repeat subpages as a list), `MiniTemplatePlugin`.
- **GDPR**: `MelisFrontGdprBannerPlugin`, `MelisFrontGdprRevalidationPlugin`.
- **Search**: `MelisFrontSearchResultsPlugin`.

### View helpers (`src/View/Helper`) — used inside `.phtml` templates
- `MelisTag` — declare an **editable zone** (`$this->MelisTag(pageId, zoneId, type, default)`).
- `MelisLink` — a **SEO-friendly page link** for a page id.
- `MelisMenu` — render a site menu (via the menu plugin).
- `MelisDragDropZone` — declare a drag-drop plugin zone.
- `siteTranslate` — a **site translation** string; `SiteConfig` — a **site config** value.
- plus language/home-page link helpers.

### Navigation (`src/Navigation`)
`MelisFrontNavigation` extends Laminas' navigation factory to build a `Laminas\Navigation`
object from the **page tree** (via engine), so menu/breadcrumb helpers can render it.

---

## 6. Cross-module links (MelisFront ↔ MelisEngine / MelisCms)

### Front → Engine (the data it renders)
MelisFront consumes engine services and gateways throughout the pipeline:
`MelisEnginePage::getDatasPage()` (page + template), `MelisEngineTree` (children, links,
breadcrumb, site by page), `MelisEngineTemplateService` (template), `MelisEngineSEOService` /
`MelisEngineTablePageSeo` (title/description/canonical/301), `MelisEngineStyle` (page CSS),
`MelisEngineLang` (languages), `MelisEngineSiteService` / `MelisEngineTableSiteDomain` (domain
→ site), `MelisEngineCacheSystem` (page cache), `MelisSearch` (indexing). All templating
plugins extend engine's `MelisTemplatingPlugin`.

### Front ↔ Cms (the editable overlay)
The **`melis` render mode** is the bridge to the back-office: MelisCms previews/edits a page
by requesting it from MelisFront with `/renderMode/melis` (and `/preview` for the saved
version). `MelisFrontLayoutListener` then wraps the rendered page in the BO layout + TinyMCE
so the same live page becomes **editable in place**; `MelisPluginRendererController` re-renders
individual plugins for the editor. Conversely, `MelisFrontDeletePluginCacheListener` listens
to **MelisCms** page events (`meliscms_page_save_end`, `…publish_end`, `…delete_end`, …) to
**invalidate the Menu/Breadcrumb plugin caches** so the public site reflects edits.

### Events MelisFront fires (that others hook)
- `melisfront_site_dispatch_ready` — after page validation, before render (used by the BO and
  other modules to intervene in routing/rendering).
- layout/asset/translation service events (`melis_front_minify_assets_start`,
  `melis_translation_get_trans_by_locale_start`, …).

---

## 7. Assets (minify)

`MinifyAssets` (`MinifyAssetsService`, via `matthiasmullie/minify`) compiles a site's CSS/JS
into `bundle.css` / `bundle.js`; `MelisFrontMinifiedAssetsCheckerListener` injects them
(cache-busted by a bundle version) when present. The `/minify-assets` route triggers a build.

---

## 8. Quick code map

```
melis-front/
├── composer.json                 → deps (core + navigation/serializer/file/minify), category cms
├── config/module.config.php      → routes, services, controller plugins, view helpers, caches
├── src/
│   ├── Module.php                → bootstrap; wires the ~19 render-pipeline listeners + plugin configs
│   ├── Controller/ (+ Plugin/)   → Index, SpecialUrls, Search, PluginRenderer, Style, MinifyAssets + templating plugins
│   ├── Service/                  → MelisFrontHead, MinifyAssets, SiteConfig, SiteTranslation, Translation
│   ├── Listener/                 → SEO routes/meta, layout, page cache, 404/301, XSS, home routing, plugins-to-layout…
│   ├── View/Helper/              → MelisTag, MelisLink, MelisMenu, siteTranslate, SiteConfig, lang/home links
│   └── Navigation/Factory/       → MelisFrontNavigation (page tree → Laminas Navigation)
├── public/                       → front assets (minified bundles live per-site)
├── language/                     → translations
└── etc/                          → MarketPlace + MelisAI/doc (this doc)
```

---

## 9. Front rendering — end-to-end (summary)

1. A visitor requests a site URL → the `melis-front` regex route resolves an `idpage`.
2. `EVENT_DISPATCH` listeners: sanitize, home-routing, **validate the page** (engine), handle
   404/301, fire `melisfront_site_dispatch_ready`.
3. The page's **template** (from engine) runs; its `.phtml` uses **view helpers**
   (`MelisTag`, `MelisMenu`, `MelisLink`…) and **templating plugins** (which extend engine's
   `MelisTemplatingPlugin`) to produce content blocks.
4. `EVENT_FINISH` listeners assemble the final HTML: plugin assets → SEO meta → page CSS →
   layout → cache → minified bundles.
5. In **`melis` mode** the same flow wraps the page in the BO layout + TinyMCE so MelisCms can
   edit it in place; in `front` mode it is served (and cached) to the public.

---

*Document for AI consumption (MelisAI MCP) — describes the `melisplatform/melis-front` module
and its place in the MelisCms / MelisFront / MelisEngine trio. Last reviewed 2026-06-08
against the current source.*
