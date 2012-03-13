
(function($) {
    var inc = 1;
    $.fn.wysiwyg = function(options) {

		var self = $(this);
		$.ajax({
			dataType: 'json',
			url: 'admin/wysiwyg/modules',
			success: function(enhancers) {
				options = $.extend({
					// Location of TinyMCE script
					script_url : '/static/cms/js/vendor/tinymce/tiny_mce_src.js',
					theme      : 'nos',
					plugins    : 'pdw,spellchecker,template,xhtmlxtras,style,layer,table,save,advhr,advlist,inlinepopups,media,searchreplace,paste,noneditable,visualchars,contextmenu',
                    pdw_toggle_on : 1,
                    pdw_toggle_toolbars : "3",
					theme_nos_enhancers : enhancers
				}, options || {});

				$(self).tinymce(options);

			}
		});
    };
})(jQuery);