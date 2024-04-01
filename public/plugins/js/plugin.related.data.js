var pluginRelatedData = (function($, window) {
    /**
     * Process el from .html-editable on blur event
     * Need processPluginData(toolBox, percentTotalWidth )
     * */
    function setPercentTotalWidth( el ) {
        //setTimeout(function() {
            // .melis-dragdropzone find .children(".melis-plugin-tools-box")
            var $melisUI            = el.closest(".melis-ui-outlined"),
                melisDndZone        = $melisUI.parent(),
                $toolBox            = $melisUI.children(".melis-plugin-tools-box"),
                melisUIWidth        = $melisUI.outerWidth(), // totalWidth
                dragDropZoneWidth   = $(melisDndZone).outerWidth(); // parentWidth

            var percentTotalWidth = ( 100 * dragDropZoneWidth / melisUIWidth );
                percentTotalWidth = percentTotalWidth.toFixed(2);
                
                //if ( $toolBox.length > 0 ) {
                    processPluginData( $toolBox, percentTotalWidth );
                //}
        //}, 1000);
    }

    function processPluginData( el, percentTotalWidth ) {
        var toolBox         = el,
            mobileWidth, tabletWidth, desktopWidth, currentClass, newClass,
            iframe          = window.parent.$("#"+ parent.activeTabId).find('iframe'),
            parentOutlined  = $(toolBox).closest(".melis-ui-outlined"),
            classes         = parentOutlined.attr("class").split(" "),
            editable        = parentOutlined.find(".melis-editable");

            if ( toolBox.length ) {
                var pluginList = new Object();
                    // get data first load
                    $( toolBox ).map(function() {
                        var $this = $(this);

                            pluginList['melisIdPage']       = window.parent.$("#"+parent.activeTabId).find(".melis-iframe").data("iframe-id");
                            pluginList['melisModule']       = $this.data("module");
                            pluginList['melisPluginName']   = $this.data("plugin");
                            pluginList['melisPluginId']     = $this.data("plugin-id");
                            pluginList['melisPluginTag']    = $this.data("melis-tag");
                            mobileWidth                     = $this.attr("data-plugin-width-mobile");
                            tabletWidth                     = $this.attr("data-plugin-width-tablet");
                            desktopWidth                    = $this.attr("data-plugin-width-desktop");
                    });

                    // custom action check if plugin tags, uncommented for checking, 03262024
                    /* if( $(editable).length ) {
                        // trigger focus to saveSession
                        var data = $(editable).data();
                            $(editable).focus().removeClass("mce-edit-focus");

                        // hide tinymce option while resizing
                        var inst = tinyMCE.EditorManager.get(data.pluginId);
                            inst.fire("blur");
                            iframe.blur();

                            $(editable).map(function() {
                                var $this = $(this);

                                    pluginList['tagType']   =   $this.data("tag-type");
                                    pluginList['tagId']     =   $this.data("tag-id");
                                    pluginList['tagValue']  =   tinyMCE.activeEditor.getContent({format : 'html'});
                            });
                    } */

                    // check if resize in mobile
                    if(iframe.width() <= 480) {
                        mobileWidth  = percentTotalWidth;
                        // update DOM data attribute
                        $(toolBox).attr("data-plugin-width-mobile", mobileWidth);
                        currentClass = "plugin-width-xs-";

                        var strPercentTotalWidth = percentTotalWidth;
                        // newClass = "plugin-width-xs-"+Math.floor(percentTotalWidth); // removed when css is ready
                        newClass = "plugin-width-xs-"+strPercentTotalWidth.replace(".", "-"); // removed when css is ready
                        $.each(classes, function(key, value) {
                            if( value.indexOf(currentClass) != -1 ) {
                                parentOutlined.removeClass(value).addClass(newClass);
                            }
                        });
                    }
                    // check if resize in tablet
                    if(iframe.width() > 490 && iframe.width() <= 980) {
                        tabletWidth = percentTotalWidth;
                        $(toolBox).attr("data-plugin-width-tablet", tabletWidth);
                        currentClass = "plugin-width-md-";
                        var strPercentTotalWidth = percentTotalWidth;
                        // newClass = "plugin-width-md-"+Math.floor(percentTotalWidth); // removed when css is ready
                        newClass = "plugin-width-lg-"+strPercentTotalWidth.replace(".", "-"); // removed when css is ready
                        $.each(classes, function(key, value) {
                            if( value.indexOf(currentClass) != -1 ) {
                                parentOutlined.removeClass(value).addClass(newClass);
                            }
                        });
                    }
                    // check if resize in desktop
                    if(iframe.width() >= 981) {
                        desktopWidth = percentTotalWidth;
                        $(toolBox).attr("data-plugin-width-desktop", desktopWidth);
                        currentClass = "plugin-width-lg-";
                        var strPercentTotalWidth = percentTotalWidth;
                        // newClass = "plugin-width-lg-"+Math.floor(percentTotalWidth); // removed when css is ready
                        newClass = "plugin-width-lg-" + strPercentTotalWidth.replace(".", "-"); // removed when css is ready
                        $.each(classes, function(key, value) {
                            if( value.indexOf(currentClass) != -1 ) {
                                parentOutlined.removeClass(value).addClass(newClass);
                            }
                        });
                    }

                    // set data attribute for width
                    pluginList['melisPluginMobileWidth'] = mobileWidth;
                    pluginList['melisPluginTabletWidth'] = tabletWidth;
                    pluginList['melisPluginDesktopWidth'] = desktopWidth;
                    pluginList['resize'] = true;

                    // recalculate frame height
                    melisPluginEdition.calcFrameHeight();

                    // pass is to savePageSession
                    melisPluginEdition.savePluginUpdate(pluginList, toolBox.data("site-module"));

                    // get plugin ID and re init
                    // check if owl re init
                    var owlCheck = $(parentOutlined).find(".owl-carousel");
                        if( $(owlCheck).length ) {
                            // setTimeout to re init, conflict with transition need to timeout
                            setTimeout(function() {
                                $(owlCheck).trigger('refresh.owl.carousel');
                            }, 500);
                        }
            }
    }

    return {
        setPercentTotalWidth    : setPercentTotalWidth
    }
})(jQuery, window);