# melis-front

MelisFront is the engine that displays website hosted on Melis Platform.  
It deals with showing pages, plugins, URL rewritting, search optimization and SEO, etc.  

## Getting Started

These instructions will get you a copy of the project up and running on your machine.  

### Prerequisites

You will need to install melisplatform/melis-engine and melisplatform/melis-asset-manager in order to have this module running.  
This will automatically be done when using composer.

### Installing

Run the composer command:
```
composer require melisplatform/melis-front
```

## Tools & Elements provided

* Services to update SEO and ressources from templating plugins  
* Listenners for SEO, 404 and URL rewritting  
* Templating plugins: MelisTag, Breadcrumb, Menu, Search, ListFolder, Drag'n'drop zone  
* Special URLs, sitemap  


## Running the code

**[See Full documentation on implementing a website here](https://www.melistechnology.com/MelisTechnology/resources/documentation/front-office/create-a-website/Declareavhostforyourwebsite)**


### MelisFront Templating Plugins  

MelisFront provides many plugins to be used in page's edition:   

* MelisFrontTagHtmlPlugin / MelisFrontTagTextareaPlugin / MelisFrontTagMediaPlugin  
This plugin must be called through the use of its helper: MelisTagsHelper.  
File: /melis-front/src/View/Helper/MelisTagsHelper.php  
```
// This code will display an editable zone, linked to the current page
// with id about_part1_text, will load the html plugin and therefore a tinyMCE HTML configuration
echo $this->MelisTag($this->idPage, 'about_part1_text', 'html', 
                	 '<div>The default text to be shown in the back office</div>')   	
```

* MelisFrontMenuPlugin  
This plugin is made to generate menu for websites.  
File: /melis-front/src/Controller/Plugin/MelisFrontMenuPlugin.php  
```
// Get the plugin
$menuPlugin = $this->MelisFrontMenuPlugin();

// Add some parameters
$menuParameters = array(
	'template_path' => 'MelisDemoCms/plugin/menu', // template to use for rendering
	'pageIdRootMenu' => 1,  // If homepage is ID 1
);
// render the view
$menu = $menuPlugin->render($menuParameters);

// Add the generated view as a child view by the name "siteMenu"
$this->layout()->addChild($menu, 'siteMenu');
```

* MelisFrontBreadcrumbPlugin  
This plugin is made to generate a breadcrumb for websites. 
File: /melis-front/src/Controller/Plugin/MelisFrontBreadcrumbPlugin.php  
```
// Get the plugin
$breadcrumbPlugin = $this->MelisFrontBreadcrumbPlugin();

// Add some parameters
$breadcrumbParameters = array(
	'template_path' => 'MelisDemoCms/plugin/breadcrumb', // template to use for rendering
	'pageIdRootBreadcrumb' => 1,// If homepage is ID 1
);

// render the view
$breadcrumb = $breadcrumbPlugin->render($breadcrumbParameters);

// Add the generated view as a child view by the name "pageBreadcrumb"
$this->layout()->addChild($breadcrumb, 'pageBreadcrumb');
```

* MelisFrontShowListFromFolderPlugin  
This plugin is made to list subpages (folder) from the treeview.  
It can be used to list subpages as a submenu and bring some content as well.   
File: /melis-front/src/Controller/Plugin/MelisFrontShowListFromFolderPlugin.php  
```
// Get the plugin
$showListForFolderPlugin = $this->MelisFrontShowListFromFolderPlugin();

// Add some parameters
$menuParameters = array(
    'template_path' => 'MelisDemoCms/plugin/testimonial-slider', // will list the subpages with a slider style template
    'pageIdFolder' => 2, // will list subpages of page 2
);

// render the view
$listView = $showListForFolderPlugin->render($menuParameters);

// Add the generated view as a child view by the name "testimonialList"
$this->view->addChild($listView, 'testimonialList');
```

* MelisFrontSearchResultsPlugin  
This plugin is made to display the search results based on ZEnd_Search.  
File: /melis-front/src/Controller/Plugin/MelisFrontSearchResultsPlugin.php  
```
// Get the plugin
$searchResults = $this->MelisFrontSearchResultsPlugin();

// Add some parameters
$searchParameters = array(
    'template_path' => 'MelisDemoCms/plugin/search-results', // template used
    'siteModuleName' => 'MelisDemoCms', // Site Index to search in
    'pagination' => array(  // pagination parameters
        'nbPerPage' => 10, 
        'nbPageBeforeAfter' => 3
    ),
);
// render the view
$searchView = $searchResults->render($searchParameters);

// Add the generated view as a child view by the name "searchresults"
$this->view->addChild($searchView, 'searchresults');
```

* MelisFrontDragDropZonePlugin  
This plugin must be called through the use of its helper: MelisDragDropZoneHelper.  
File: /melis-front/src/View/Helper/MelisDragDropZoneHelper.php  
```
// Creation of a dragdropzone link to the pageId and with id "dragdropzone_zone_1"
echo $this->MelisDragDropZone($this->idPage, "dragdropzone_zone_1");
```

**[See Full documentation on templating plugins here](https://www.melistechnology.com/MelisTechnology/resources/documentation/front-office/create-a-templating-plugin/Principle)**

### MelisFront Services  

MelisFront provides many services to be used in other modules:  

* MelisSiteConfigService  
Provides services to retrieve the config for your sites.  
File: `/melis-front/src/Service/MelisSiteConfigService.php`  

    `MelisFrontSiteConfigListener` used to update the site's config on the regular config service by merging the config from the file and the one on the database.
    * `getSiteConfigByKey(key, section = 'sites', language = null)`  
    This function retrieves a specific config by key.  
    
        Parameter    | Type       |Description
        ------------ | ---------- |-----
        key          | String     |Key of the config.
        pageId       | Int        |Used determine the site id, name, and language and on where to get the config
        section      | String/Int |The section on where to get the config or site Id
        language     | String     |Language on which to get the config  
        
        To call the service. 
        ```
        $siteConfigSvc = $this->getServiceLocator()->get('MelisSiteConfigService');
        ```
        To get a specific `key` of the current site and the language of the page with id 1
        ```
        $siteConfigSvc = $this->getServiceLocator()->get('MelisSiteConfigService');

        $config = $siteConfigSvc->getSiteConfigByKey('key', 1);
        ```
        But what if we wanted to get the key from another language of the current site? We can achieve this by defining the language on where to get the config.
        ```
        $siteConfigSvc = $this->getServiceLocator()->get('MelisSiteConfigService');
                
        $config = $siteConfigSvc->getSiteConfigByKey('key', 1,'sites', 'fr');
        // The language of the page is now overridden by the specified language.
        ```  
        We can also get a particular `key` from another site by using the `site Id`.  
        ```
        $siteConfigSvc = $this->getServiceLocator()->get('MelisSiteConfigService');
        
        $config = $siteConfigSvc->getSiteConfigByKey('key', 1, 1);
        // Return all the values of the specified key from all languages from the site with id 1.
        // The expected output is an array of values from different languages
        
        $config = $siteConfigSvc->getSiteConfigByKey('key', 1, 1, 'fr');
        // Return all the values of the specified key for the French language from the site with id 1.
        ```
        There is also a different section apart from sites. Currently, we have two sections which are sites and allSites.
        ```
        $siteConfigSvc = $this->getServiceLocator()->get('MelisSiteConfigService');
        
        $config = $siteConfigSvc->getSiteConfigByKey('key', 1, 'allSites');
        // Returns the key from the allSites section of the config
        // Language for the page is not applied but still used to get the site id and name to map for the config
        ```
* MelisSiteTranslationService  
  Provides services to translate text and list all site translations  
  File: `/melis-front/src/Service/MelisSiteTranslationService.php`  
  
  * `getText(translationKey, langId, siteId)`  .
    
    Parameter      | Type    |Description
    ------------   |-------- | -----
    translationKey | String  | Key of the translation.
    langId         | Int     | An identifier on which language to get the translation
    siteId         | Int     | An identifier on which site to get the translation
    
    To call the service.
    ```
    $melisSiteTranslationSvc = $this->getServiceLocator()->get('MelisSiteTranslationService');
    ```
    To get a particular translation, You need to specify the translation key along with the lang id and site id.
    ```
    $test = $melisSiteTranslationService->getText('key', 1, 1);
    // Retrieves the translation for the language id 1 and site id 1.
    ```
  
### View Helpers

Melis Front View Helpers:  

* MelisTagsHelper: When called it will create an editable zone in the template of the page.  
The tag must take 3 parameters: the id of page, its own id (unique) and a default text that will be displayed (used when no text has been filled into the zone, so that something is displayed and the template still looks like a template).  
File: /melis-front/src/View/Helper/MelisTagsHelper.php  
```
// This code will display an editable zone, linked to the current page
// with id about_part1_text, will load the html plugin and therefore a tinyMCE HTML configuration
echo $this->MelisTag($this->idPage, 'about_part1_text', 'html', 
                	 '<div>The default text to be shown in the back office</div>')   	
```

* MelisLinksHelper: When called it will generate a link to a Melis page, following all rules and possible SEO 
File: /melis-front/src/View/Helper/MelisLinksHelper.php  
```
// This call will generate a link to pageId 1 and it will be an absolute URL including the domain
echo $this->MelisLink(1, true);
```

* MelisDragDropZoneHelper  
File: /melis-front/src/View/Helper/MelisDragDropZoneHelper.php  
```
// Creation of a dragdropzone link to the pageId and with id "dragdropzone_zone_1"
echo $this->MelisDragDropZone($this->idPage, "dragdropzone_zone_1");
```
* MelisSiteConfigHelper  
This helper is used to get a specific config for a site.  
File: `/melis-front/src/View/Helper/MelisDragDropZoneHelper.php`  
Function: `SiteConfig(key, sectiom = 'sites', language = null)`

    Parameter    | Type       | Description
    ------------ | ---------- | ------
    key          | String     |Key of the config.
    section      | String/Int |The section on where to get the config or site Id
    language     | String     |Language on which to get the config  
    
    To call the helper. 
    ```
    $this->SiteConfig('key');
    ```
    To get a `specific key` from the config for the `current site`.
    ```
    $config = $this->SiteConfig('key');
    ```
    But what if we wanted to get the `key` from another `language` of the `current site`? We can achieve this by defining the `language` on where to get the `config`.  
    ```    
    $config = $this->SiteConfig('key', 'sites', 'fr');
    // The language of the page is now overridden by the specified language.
    ```  
    We can also get a particular `key` from another site by using the `site Id`.
    ```
    $config = $this->SiteConfig('key', 1);
    // Return all the values of the specified key from all languages from the site with id 1.
    // The expected output is an array of values from different languages
    
    $config = $this->SiteConfig('key', 1, 'fr');
    // Return all the values of the specified key for the French language from the site with id 1.
    ```
    There is also a different `section` apart from `sites`. Currently, we have two sections which are `sites` and `allSites`.  
    ```
    $config = $this->SiteConfig('key', 'allSites');
    // Returns the key from the allSites section of the config
    ```
* MelisSiteTranslation  
This helper is used to get a specific translation for a site.  
File: `/melis-front/src/View/Helper/MelisSiteTranslationHelper.php`  
Function: `getText(translationkey, langId, siteId)`  

    Parameter      | Type    |Description
    ------------   |-------- | -----
    translationKey | String  | Key of the translation.
    langId         | Int     | An identifier on which language to get the translation
    siteId         | Int     | An identifier on which site to get the translation
    
    To call the helper method.
    ```
    $this->SiteTranslation('translationKey', 'langId', 'siteId');
    ```
    To get a particular translation, You need to specify the translation key along with the lang id and site id.
    ```
    $text = $this->SiteTranslation('key', 1, 1);
    // Retrieves the translation for the language id 1 and site id 1.
    ```

### Special URLs

MelisFront is using the following URLs as defaults:  

* URLs of pages: /.*/id/(?<idpage>[0-9]+)  
If no speacial naming is used in SEO URLs, pages use this system  
* URLs of pages when displayed in BO: /.*/id/(?<idpage>[0-9]+)/renderMode/melis  
Beeing logged in the back office is of course mandatory  
* URLs of pages in preview mode (saved version): /.*/id/(?<idpage>[0-9]+)/preview  
Beeing logged in the back office is of course mandatory  
* SiteMap: /sitemap.html|sitemap.xml|sitemap  
Will display the sitemap based on the Navigation class contained in MelisFront module.  
Site to map will be found thanks to the domain used.  
* Search Indexer: /melissearchindex/module[/:moduleName]/pageid[/:pageid]/exclude-pageid[/:expageid]  
This will launch the indexer. moduleName is the site's module name, pageid the id where to start crawling and expageid will exclude some specific page ids from being indexed.  


## Authors

* **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-front/contributors) who participated in this project.


## License

This project is licensed under the OSL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details