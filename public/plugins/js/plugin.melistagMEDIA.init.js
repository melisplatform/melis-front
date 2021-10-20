function melistagMEDIA_init(idPlugin) {
	// declaring parameters variable for old / cross browser compatability
	if (typeof idPlugin === "undefined") idPlugin = null;

	var tinyMceOption = {
		templates:
			"/melis/MelisCms/PageEdition/getTinyTemplates?idPage=" +
			melisActivePageId,
	};

	if (idPlugin != null) {
		tinyMceOption.setup = "melistagMEDIA_savePlugin";
	}

	var idPlugin = idPlugin !== null ? "#" + idPlugin : "div.media-editable";

	// Editor the will use for Pages
	melisTinyMCE.createTinyMCE("media", idPlugin, tinyMceOption);
}

function melistagMEDIA_savePlugin(editor) {
	editor.on("init", function(ed) {
		$("#" + editor.id).trigger("blur");
	});
}

// run this function when you click out of the tinymce
$("body").on("blur", "div.media-editable", function() {
	// get all data-attributes from the clicked
	var currentdata = $(this).data();
	// var currentdata = $(this).closest(".html-editable").data();

	var pluginDivId = $(this).closest('.media-editable').attr('id');
	
	var content = tinyMCE.get(pluginDivId).getContent({ format: "html" });

	// get the content of the active tinyMCE editor.
	// var content = tinyMCE.activeEditor.getContent({ format: "html" });

	var data = {
		melisPluginName: currentdata.plugin,
		melisPluginTag: currentdata.melisTag,
		melisPluginId: currentdata.tagId,
		tagType: currentdata.tagType,
		tagId: currentdata.tagId,
		tagValue: content,
	};
	melisPluginEdition.calcFrameHeight();
	melisPluginEdition.savePluginUpdate(data, currentdata.siteModule);
});

melistagMEDIA_init();
