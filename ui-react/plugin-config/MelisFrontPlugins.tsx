// Full-React config TABS for every MelisFront plugin that has a modal_form. Source lives in melis-front
// (this module), imported into melis-cms's SPA build and registered into the shared tab registry. Each
// tab reads/writes the SHARED values map via `ctx` (self-prefilling field components), and one Save posts
// everything to the plugin's savePluginConfigToXml() (byte-compatible XML). Other modules can add tabs to
// any of these plugins the same way (registerPluginTab). Mirrors each plugin's legacy modal_form.
import {
  registerPluginTab, type PluginTabContext,
  TemplateField, PageField, TextField, RemoteSelectField, HiddenField,
} from '../../../melis-cms/ui-react/src/PluginFormKit'

const COG = 'fa fa-cog'

/* ── Breadcrumb ── template + page racine ────────────────────────────────── */
function BreadcrumbProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint="Gabarit de rendu du fil d'Ariane." />
    <PageField ctx={ctx} name="pageIdRootBreadcrumb" label="Page racine" hint="Le fil d'Ariane démarre à cette page." placeholder="Choisir la page racine…" />
  </div>)
}

/* ── GDPR banner ── template only ────────────────────────────────────────── */
function GdprBannerProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div><TemplateField ctx={ctx} hint="Gabarit de rendu de la bannière RGPD." /></div>)
}

/* ── GDPR revalidation ── template + site + gdpr module ──────────────────── */
function GdprRevalidationProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} />
    <RemoteSelectField ctx={ctx} name="site_id" label="Site" hint="Le site concerné par la revalidation RGPD." />
    <RemoteSelectField ctx={ctx} name="module" label="Module GDPR" hint="Le module qui gère le consentement." />
  </div>)
}

/* ── Generic content ── parent page ──────────────────────────────────────── */
function GenericContentProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <PageField ctx={ctx} name="parentPageId" label="Page parente" hint="La page dont le contenu est affiché." placeholder="Choisir la page parente…" />
  </div>)
}

/* ── Menu ── template + root page ────────────────────────────────────────── */
function MenuProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint="Gabarit de rendu du menu." />
    <PageField ctx={ctx} name="pageIdRootMenu" label="Page racine du menu" hint="Le menu liste les pages sous cette racine." placeholder="Choisir la page racine…" />
  </div>)
}

/* ── Block section (Block) ── template only ──────────────────────────────── */
function BlockSectionProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div><TemplateField ctx={ctx} hint="Gabarit de rendu du bloc." /></div>)
}

/* ── Show list from folder (Folder listing) ── template + source page ────── */
function ShowListFromFolderProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint="Gabarit de rendu de la liste." />
    <PageField ctx={ctx} name="pageIdFolder" label="Dossier / page source" hint="La liste affiche les pages sous cette page." placeholder="Choisir la page source…" />
  </div>)
}

/* ── Search results ── properties + pagination ───────────────────────────── */
function SearchResultsProps({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint="Gabarit de rendu des résultats." />
    <RemoteSelectField ctx={ctx} name="siteModuleName" label="Module du site" hint="Le module dont on cherche le contenu." />
    <TextField ctx={ctx} name="keyword" label="Mot-clé par défaut" hint="Recherche pré-remplie (facultatif)." />
  </div>)
}
function SearchResultsPagination({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <HiddenField ctx={ctx} name="current" />
    <TextField ctx={ctx} name="nbPerPage" label="Résultats par page" type="number" hint="Nombre de résultats affichés par page." />
    <TextField ctx={ctx} name="nbPageBeforeAfter" label="Pages avant / après" type="number" hint="Nombre de liens de page de part et d'autre de la page courante." />
  </div>)
}

/** Register every MelisFront plugin's native config tab(s). Called from melis-cms's PluginForms registry. */
export function registerMelisFrontPlugins(): void {
  registerPluginTab('MelisFrontBreadcrumbPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: BreadcrumbProps })
  registerPluginTab('MelisFrontGdprBannerPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: GdprBannerProps })
  registerPluginTab('MelisFrontGdprRevalidationPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: GdprRevalidationProps })
  registerPluginTab('MelisFrontGenericContentPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: GenericContentProps })
  registerPluginTab('MelisFrontMenuPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: MenuProps })
  registerPluginTab('MelisFrontBlockSectionPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: BlockSectionProps })
  registerPluginTab('MelisFrontShowListFromFolderPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: ShowListFromFolderProps })
  registerPluginTab('MelisFrontSearchResultsPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: SearchResultsProps })
  registerPluginTab('MelisFrontSearchResultsPlugin', { id: 'pagination', title: 'Pagination', icon: 'fa fa-th-list', order: 1, Component: SearchResultsPagination })
}
