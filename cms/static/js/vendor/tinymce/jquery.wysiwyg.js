
(function($) {
    var inc = 1;
    $.fn.wysiwyg = function(options) {

		var self = $(this);
		$.ajax({
			dataType: 'json',
			url: 'admin/wysiwyg/modules',
			success: function(modules) {
				options = $.extend({
					// Location of TinyMCE script
					script_url : '/static/cms/js/vendor/tinymce/tiny_mce_src.js',
					theme      : 'nos',
					plugins    : 'template,xhtmlxtras,style,layer,table,save,advhr,advlist,inlinepopups,media,searchreplace,paste,noneditable,visualchars,contextmenu',
					theme_nos_modules : modules
				}, options || {});

				$(self).tinymce(options);

			}
		});
    };
})(jQuery);