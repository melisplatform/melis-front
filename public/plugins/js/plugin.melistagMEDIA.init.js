function melistagMEDIA_init(idPlugin){
    // declaring parameters variable for old / cross browser compatability
    if(typeof idPlugin === "undefined") idPlugin = null;

	var tinyMceOption = new Array;
	
	if(idPlugin != null){
		tinyMceOption = {setup: 'melistagMEDIA_savePlugin'};
	}
	
	var idPlugin = idPlugin !== null ?  "#"+idPlugin : "div.media-editable";

	// Editor the will use for Pages
	melisTinyMCE.createTinyMCE("media", idPlugin, tinyMceOption);
}

function melistagMEDIA_savePlugin(editor){
	editor.on("init",function(ed) {
		$("#"+editor.id).trigger("blur");
    });
}

// run this function when you click out of the tinymce
$("body").on("blur", "div.media-editable", function(){
	
	// get all data-attributes from the clicked
	var currentdata = $(this).data();
	
	// get the content of the active tinyMCE editor.
	var content = tinyMCE.activeEditor.getContent({format : 'html'});
	
	var data = {
		melisPluginName: currentdata.plugin,
		melisPluginTag: currentdata.melisTag,
		melisPluginId : currentdata.tagId,
		tagType: currentdata.tagType,
		tagId : currentdata.tagId,
		tagValue : content,
    };
    melisPluginEdition.calcFrameHeight();
    melisPluginEdition.savePluginUpdate(data);
});


melistagMEDIA_init();
