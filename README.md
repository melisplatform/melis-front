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


### View Helpers

Melis Front comes with 3 View Helpers:  

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