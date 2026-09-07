// Full-React config TABS for every MelisFront plugin that has a modal_form. Source lives in melis-front
// (this module), imported into melis-cms's SPA build and registered into the shared tab registry. Each
// tab reads/writes the SHARED values map via `ctx` (self-prefilling field components), and one Save posts
// everything to the plugin's savePluginConfigToXml() (byte-compatible XML). Other modules can add tabs to
// any of these plugins the same way (registerPluginTab). Mirrors each plugin's legacy modal_form.
import {
  registerPluginTab, type PluginTabContext,
  TemplateField, PageField, TextField, RemoteSelectField, HiddenField,
} from '../../../melis-cms/ui-react/src/PluginFormKit'
import { peLang } from '../../../melis-cms/ui-react/src/page-editor-i18n'

// Local bilingual dict — this React brick does NOT share the host i18n dictionary (same pattern as
// page-editor-i18n). Selected once by the current back-office language (<html lang>).
const L = ({
  fr: {
    // ── tab titles ──
    tabProperties: 'Propriétés',
    tabPagination: 'Pagination',
    // ── Breadcrumb ──
    breadcrumbTemplateHint: "Gabarit de rendu du fil d'Ariane.",
    breadcrumbRootPage: 'Page racine',
    breadcrumbRootPageHint: 'Le fil d\'Ariane démarre à cette page.',
    breadcrumbRootPagePlaceholder: 'Choisir la page racine…',
    // ── GDPR banner ──
    gdprBannerTemplateHint: 'Gabarit de rendu de la bannière RGPD.',
    // ── GDPR revalidation ──
    gdprRevalSite: 'Site',
    gdprRevalSiteHint: 'Le site concerné par la revalidation RGPD.',
    gdprRevalModule: 'Module GDPR',
    gdprRevalModuleHint: 'Le module qui gère le consentement.',
    // ── Generic content ──
    genericParentPage: 'Page parente',
    genericParentPageHint: 'La page dont le contenu est affiché.',
    genericParentPagePlaceholder: 'Choisir la page parente…',
    // ── Menu ──
    menuTemplateHint: 'Gabarit de rendu du menu.',
    menuRootPage: 'Page racine du menu',
    menuRootPageHint: 'Le menu liste les pages sous cette racine.',
    menuRootPagePlaceholder: 'Choisir la page racine…',
    // ── Block section ──
    blockTemplateHint: 'Gabarit de rendu du bloc.',
    // ── Show list from folder ──
    folderTemplateHint: 'Gabarit de rendu de la liste.',
    folderSourcePage: 'Dossier / page source',
    folderSourcePageHint: 'La liste affiche les pages sous cette page.',
    folderSourcePagePlaceholder: 'Choisir la page source…',
    // ── Search results ──
    searchTemplateHint: 'Gabarit de rendu des résultats.',
    searchSiteModule: 'Module du site',
    searchSiteModuleHint: 'Le module dont on cherche le contenu.',
    searchKeyword: 'Mot-clé par défaut',
    searchKeywordHint: 'Recherche pré-remplie (facultatif).',
    // ── Search results pagination ──
    searchPerPage: 'Résultats par page',
    searchPerPageHint: 'Nombre de résultats affichés par page.',
    searchBeforeAfter: 'Pages avant / après',
    searchBeforeAfterHint: "Nombre de liens de page de part et d'autre de la page courante.",
  },
  en: {
    // ── tab titles ──
    tabProperties: 'Properties',
    tabPagination: 'Pagination',
    // ── Breadcrumb ──
    breadcrumbTemplateHint: 'Rendering template for the breadcrumb.',
    breadcrumbRootPage: 'Root page',
    breadcrumbRootPageHint: 'The breadcrumb starts at this page.',
    breadcrumbRootPagePlaceholder: 'Choose the root page…',
    // ── GDPR banner ──
    gdprBannerTemplateHint: 'Rendering template for the GDPR banner.',
    // ── GDPR revalidation ──
    gdprRevalSite: 'Site',
    gdprRevalSiteHint: 'The site concerned by the GDPR revalidation.',
    gdprRevalModule: 'GDPR module',
    gdprRevalModuleHint: 'The module that handles consent.',
    // ── Generic content ──
    genericParentPage: 'Parent page',
    genericParentPageHint: 'The page whose content is displayed.',
    genericParentPagePlaceholder: 'Choose the parent page…',
    // ── Menu ──
    menuTemplateHint: 'Rendering template for the menu.',
    menuRootPage: 'Menu root page',
    menuRootPageHint: 'The menu lists the pages under this root.',
    menuRootPagePlaceholder: 'Choose the root page…',
    // ── Block section ──
    blockTemplateHint: 'Rendering template for the block.',
    // ── Show list from folder ──
    folderTemplateHint: 'Rendering template for the list.',
    folderSourcePage: 'Folder / source page',
    folderSourcePageHint: 'The list shows the pages under this page.',
    folderSourcePagePlaceholder: 'Choose the source page…',
    // ── Search results ──
    searchTemplateHint: 'Rendering template for the results.',
    searchSiteModule: 'Site module',
    searchSiteModuleHint: 'The module whose content is searched.',
    searchKeyword: 'Default keyword',
    searchKeywordHint: 'Pre-filled search (optional).',
    // ── Search results pagination ──
    searchPerPage: 'Results per page',
    searchPerPageHint: 'Number of results shown per page.',
    searchBeforeAfter: 'Pages before / after',
    searchBeforeAfterHint: 'Number of page links on each side of the current page.',
  },
} as const)[peLang()]

const COG = 'fa fa-cog'

/* ── Breadcrumb ── template + page racine ────────────────────────────────── */
function BreadcrumbProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint={L.breadcrumbTemplateHint} />
    <PageField ctx={ctx} name="pageIdRootBreadcrumb" label={L.breadcrumbRootPage} hint={L.breadcrumbRootPageHint} placeholder={L.breadcrumbRootPagePlaceholder} />
  </div>)
}

/* ── GDPR banner ── template only ────────────────────────────────────────── */
function GdprBannerProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div><TemplateField ctx={ctx} hint={L.gdprBannerTemplateHint} /></div>)
}

/* ── GDPR revalidation ── template + site + gdpr module ──────────────────── */
function GdprRevalidationProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} />
    <RemoteSelectField ctx={ctx} name="site_id" label={L.gdprRevalSite} hint={L.gdprRevalSiteHint} />
    <RemoteSelectField ctx={ctx} name="module" label={L.gdprRevalModule} hint={L.gdprRevalModuleHint} />
  </div>)
}

/* ── Generic content ── parent page ──────────────────────────────────────── */
function GenericContentProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <PageField ctx={ctx} name="parentPageId" label={L.genericParentPage} hint={L.genericParentPageHint} placeholder={L.genericParentPagePlaceholder} />
  </div>)
}

/* ── Menu ── template + root page ────────────────────────────────────────── */
function MenuProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint={L.menuTemplateHint} />
    <PageField ctx={ctx} name="pageIdRootMenu" label={L.menuRootPage} hint={L.menuRootPageHint} placeholder={L.menuRootPagePlaceholder} />
  </div>)
}

/* ── Block section (Block) ── template only ──────────────────────────────── */
function BlockSectionProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div><TemplateField ctx={ctx} hint={L.blockTemplateHint} /></div>)
}

/* ── Show list from folder (Folder listing) ── template + source page ────── */
function ShowListFromFolderProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint={L.folderTemplateHint} />
    <PageField ctx={ctx} name="pageIdFolder" label={L.folderSourcePage} hint={L.folderSourcePageHint} placeholder={L.folderSourcePagePlaceholder} />
  </div>)
}

/* ── Search results ── properties + pagination ───────────────────────────── */
function SearchResultsProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint={L.searchTemplateHint} />
    <RemoteSelectField ctx={ctx} name="siteModuleName" label={L.searchSiteModule} hint={L.searchSiteModuleHint} />
    <TextField ctx={ctx} name="keyword" label={L.searchKeyword} hint={L.searchKeywordHint} />
  </div>)
}
function SearchResultsPagination({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <HiddenField ctx={ctx} name="current" />
    <TextField ctx={ctx} name="nbPerPage" label={L.searchPerPage} type="number" hint={L.searchPerPageHint} />
    <TextField ctx={ctx} name="nbPageBeforeAfter" label={L.searchBeforeAfter} type="number" hint={L.searchBeforeAfterHint} />
  </div>)
}

/** Register every MelisFront plugin's native config tab(s). Called from melis-cms's PluginForms registry. */
export function registerMelisFrontPlugins(): void {
  registerPluginTab('MelisFrontBreadcrumbPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: BreadcrumbProps })
  registerPluginTab('MelisFrontGdprBannerPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: GdprBannerProps })
  registerPluginTab('MelisFrontGdprRevalidationPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: GdprRevalidationProps })
  registerPluginTab('MelisFrontGenericContentPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: GenericContentProps })
  registerPluginTab('MelisFrontMenuPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: MenuProps })
  registerPluginTab('MelisFrontBlockSectionPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: BlockSectionProps })
  registerPluginTab('MelisFrontShowListFromFolderPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: ShowListFromFolderProps })
  registerPluginTab('MelisFrontSearchResultsPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: SearchResultsProps })
  registerPluginTab('MelisFrontSearchResultsPlugin', { id: 'pagination', title: L.tabPagination, icon: 'fa fa-th-list', order: 1, Component: SearchResultsPagination })
}
