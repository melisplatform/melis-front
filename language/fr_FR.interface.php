<?php
/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

return [
    
    // Plugins
    'tr_meliscms_Plugins' => 'Plugins',
    'tr_melisfront_generate_error_No module or plugin or idpage parameters' => 'Pas de parametres module/plugin/pageid',
    'tr_melisfront_generate_error_Plugin config not found' => 'Configuration plugin non trouvée',
    'tr_melisfront_generate_error_Plugin cant be created' => 'Plugin impossible à créer',
    
    'tr_meliscms_Plugins_Parameters' => 'Paramètres',
    'tr_melis_Plugins_Template_modal_description' => 'Le template permet de choisir le rendu du plugin en fonction du site.',
    'tr_melis_Plugins_Template' => 'Template',
    'tr_melis_Plugins_Template tooltip' => 'Le template permet de choisir le rendu du plugin en fonction du site',
    'tr_melis_Plugins_Choose' => 'Choisissez',
    'tr_melis_plugins_page_id' => 'Page ID',
    
    'tr_PluginSection_melisfront' => 'Melis Cms',
    'tr_MelisFrontTagPlugin_Name' => 'Melis Tags',
    'tr_MelisFrontSubcategoryTag_Title' => 'Tags',
    'tr_MelisFrontSubcategoryPageBasics_Title' => 'Basiques Page',
    'tr_MelisFrontTagHtmlPlugin_Name' => 'Html tag',
    'tr_MelisFrontTagHtmlPlugin_Description' => 'Le plugin Tag Html apporte une zone éditable wysiwig à vos templates. Cette zone est modifiable via un éditeur wysiwyg et préconfigurée pour une édition de texte riche.',
    'tr_MelisFrontTagTextareaPlugin_Name' => 'Textarea tag',
    'tr_MelisFrontTagTextareaPlugin_Description' => 'Le plugin Tag Textarea apporte une zone éditable wysiwig à vos templates. Cette zone est modifiable via un éditeur wysiwyg et préconfigurée pour une édition de texte simple.',
    'tr_MelisFrontTagMediaPlugin_Name' => 'Media tag',
    'tr_MelisFrontTagMediaPlugin_Description' => 'Le plugin Tag Media apporte une zone éditable wysiwig à vos templates. Cette zone est modifiable via un éditeur wysiwyg et préconfigurée pour une édition de média.',
    
    'tr_MelisFrontTagPlugin_Description' => 'Le plugin Tag apporte une zone éditable wysiwig à vos templates. Ces zones sont préconfigurées pour du texte simple, du html ou de la saisie de media.',
    'tr_MelisFrontBreadcrumbPlugin_Name' => 'Fil d\'ariane',
    'tr_MelisFrontBreadcrumbPlugin_Description' => 'Le plugin fil d\'ariane permet de générer un fil d\'ariane à partir de l\'arborescence des pages.',
    'tr_MelisFrontMenuPlugin_Name' => 'Menu',
    'tr_MelisFrontMenuPlugin_Description' => 'Le plugin menu permet de générer un menu à partir de l\'arborescence des pages.',
    'tr_MelisFrontShowListFromFolderPlugin_Name' => 'Listing dossier',
    'tr_MelisFrontShowListFromFolderPlugin_Description' => 'Le plugin listing dossier permet de lister les sous pages d\'un dossier ou d\'une page de l\'arborescence, permettant de créer des listes dynamiques d\'actualités ou de pages détails.',
    'tr_MelisFrontSearchResultsPlugin_Name' => 'Recherche',
    'tr_MelisFrontSearchResultsPlugin_Description' => 'Le plugin recherche liste les résultats de recherche sur la page en se basant sur le moteur interne de Melis Platform, basé sur Zend Search.',
    'tr_melis_plugins_page_id_empty' => 'Please enter the page ID',
    'tr_melis_plugins_page_id_not_num' => 'Invalid page ID, it must be numeric',
    
    
    // plugins validator messages
    'tr_front_template_path_empty' => 'Veuillez choisir un template',
    
    // Common plugin config validator messages
    'tr_front_common_input_empty' => 'Le champ ne peut être vide',
    'tr_front_common_input_not_digit' => 'Champ requis, ne peut être vide',
    
    // tabs
    'tr_front_plugin_tab_properties' => 'Propriétés',
    
    // plugin breadcrumb
    'tr_front_plugin_breadcrumb_root_page' => 'Page de départ',
    'tr_front_plugin_breadcrumb_root_page tooltip' => 'La page de départ correspond à la page d&#39;où commence le fil d&#39;ariane',
    
    
    // plugin : menu
    'tr_front_plugin_menu_modal_title' => 'Edit Menu',
    'tr_front_plugin_menu_root_page' => 'Page de départ',
    'tr_front_plugin_menu_root_page tooltip' => 'La page de départ correspond à la page parent d&#39;où commence le menu. Les sous-pages seront listées dans le menu. Pour qu&#39;une page s&#39;affiche dans le menu, sa propriété &#34;Affichage menu&#34; doit être définie sur &#34;Lien&#34; ou &#34;Texte sans lien&#34;',
    
    // listing folder
    'tr_front_plugin_showlistfromfolder_root_page' => 'Page parente',
    'tr_front_plugin_showlistfromfolder_root_page tooltip' => 'Liste les sous-page de la page parente',
    
    // plugin : search
    'tr_front_plugin_search_site' => 'Site',
    'tr_front_plugin_search_site tooltip' => 'Nom du site sur lequel s&#39;effectue la recherche',
    'tr_front_plugin_search_site_module_name' => 'Nom du module du site',
    'tr_front_plugin_search_site_module_name_empty' => 'Entrez le nom du site',
    'tr_front_plugin_search_site_keyword' => 'Mot clef par défaut',
    'tr_front_plugin_search_site_keyword tooltip' => 'Mot clef pour avoir une recherche par défaut en arrivant sur la page',
    'tr_front_plugin_search_site_keyword_empty' => 'Entrez un mot clef pour avoir une recherche par défaut en arrivant sur la page',
    
    'tr_front_plugin_search_pagination' => 'Pagination',
    'tr_front_plugin_search_pagination_nbPerPage' => 'Résultats par page',
    'tr_front_plugin_search_pagination_nbPageBeforeAfter' => 'Nombre de liens avant et après la page courante',
    
    'tr_front_plugin_search_pagination_nbPerPage tooltip' => 'Nombre de résultats s&#39;affichant sur une même page',
    'tr_front_plugin_search_pagination_nbPageBeforeAfter tooltip' => 'L&#39;affichage de la pagination génére un certains nombre de liens avant et après la page en cours, ce champ permet de limiter ce nombe de liens',

    'tr_PluginSection_MelisMiniTemplate' => 'Mini Template',
    // plugin config
    'tr_melis_front_bloc_plugin_description' => 'Le plugin Block ajoute un simple block vide',

    // plugin: GDPR banner
    'tr_melis_front_gdpr_banner_agree_en_EN' => 'OK, I understand',
    'tr_melis_front_gdpr_banner_agree_fr_FR' => 'Accepter',

    //Minify Assets
    'tr_front_minify_assets_compiled_successfully' => 'Assets minifiées avec succès',
    'tr_front_minify_assets_nothing_to_compile' => 'There is nothing to compile.',
    'tr_front_minify_assets_error_occurred' => 'An error occurred while compiling assets.',
    'tr_front_minify_assets_success' => 'Compiling assets successful.',

    // Plugins
    'tr_front_plugin_common_tab_properties' => 'Propriétés',
    'tr_front_plugin_common_no_param' => 'Aucun paramètre à éditer',

    //DragDrop Zone
    'tr_front_drag_drop_zone_label' => 'Déposez le plugin ici',

    // GDPR Revalidation plugin
    'tr_melis_front_gdpr_revalidation_name' => 'GDPR Revalidation plugin',
    'tr_melis_front_gdpr_revalidation_desc' => 'Ce plugin permet à l\'utilisateur de valider la rétention de ses données sur le site',
    'tr_melis_front_gdpr_revalidation_info_text' => 'Votre abonnement à nos services doit être revalidé en accord avec les exigences du RGPD car vous n\'avez pas été actif pendant longtemps. Le fait de ne pas revalider activera automatiquement l\'anonymisation de votre compte.',
    'tr_melis_front_gdpr_revalidation_btn_confirm' => 'Confirmer',
    'tr_melis_front_gdpr_revalidation_info_text_warning' => 'Ce lien n\'a pas l\'air de fonctionner ou n\'est pas reconnu.',
    'tr_melis_front_gdpr_revalidation_info_success_heading' => 'Succès',
    'tr_melis_front_gdpr_revalidation_info_success_sub_heading' => 'Votre compte a été validé',
    'tr_melis_front_gdpr_revalidation_label_checkbox' => 'Je souhaite revalider mon compte',
    'tr_melis_front_gdpr_revalidation_not_checked' => 'Veuillez cocher la case pour valider',

    'tr_melis_front_generic_plugin_name' => 'Remontée de contenu générique',
    'tr_melis_front_generic_plugin_description' => 'Generic Content Plugin Description',

    'tr_MelisFrontMenuBasedOnTagPlugin_Name' => 'Menu based on tag',
    'tr_MelisFrontMenuBasedOnTagPlugin_Description' => 'Menu based on tag',
    'tr_melis_Plugins_tag_to_use' => 'Tag to use',
    'tr_front_plugin_menu_title' => 'Title',
];
