/*
 * JS for MelisDragDropZone
 */

 var melisDragDropZone = (function($, window) {

 	var $body = $("body");

 })(jQuery, window);
 
function saveSessionDragDropZone()
{
	var data = {
        	plugin: 'MelisFrontDragDropZonePlugin',
        	tag: 'melisDragDropZone',
			id : '1_1',
			
    		pluginlist: [
    		    {
    		    	module: 'melisfront',
    		    	pluginName: 'MelisFrontMenuPlugin',
    		    	pluginId: 'menu_1'
    		    },
    		    {
    		    	module: 'melisfront',
    		    	pluginName: 'MelisFrontSearchResultsPlugin',
    		    	pluginId: 'search_1'
    		    }
    		]
	
        };
		
   savePluginUpdate(data);

}

