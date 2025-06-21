window.melistagTEXTAREA_init = function(idPlugin) {
	// declaring parameters variable for old / cross browser compatability
	if (typeof idPlugin === "undefined") idPlugin = null;

	var tinyMceOption = {
		mini_templates_url:
			"/melis/MelisCms/PageEdition/getTinyTemplates?idPage=" +
			melisActivePageId,
	};

	// added melisTinyMCE.tinyMceActionEvent as tinymce don't work after dragging a plugin into dragndropzone
	if (idPlugin != null) {
		tinyMceOption.setup = "melistagTEXTAREA_savePlugin, melisTinyMCE.tinyMceActionEvent";
	}

	var idPlugin = idPlugin !== null ? "#" + idPlugin : "div.textarea-editable";

	// Editor the will use for Pages
	melisTinyMCE.createTinyMCE("textarea", idPlugin, tinyMceOption);
};

function melistagTEXTAREA_savePlugin(editor) {
	editor.on("init", function(ed) {
		$("#" + editor.id).trigger("blur");
	});
}

// run this function when you click out of the tinymce
$("body").on("blur", "div.textarea-editable", function() {
	// get all data-attributes from the clicked
	// var currentdata = $(this).closest(".textarea-editable").data();
	var currentdata = $(this)[0].dataset;

	//var pluginDivId = $(this).closest('.textarea-editable').attr('id');
	var pluginDivId = $(this).attr('id');
	
	// get the content of the active tinyMCE editor.
	// var content = tinyMCE.activeEditor.getContent({ format: "html" });

	// good for multiple editor on the page
	var content = tinyMCE.get(pluginDivId).getContent({ format: "html" });

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

melistagTEXTAREA_init();
