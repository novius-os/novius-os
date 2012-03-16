
(function($) {
    var inc = 1;
    $.fn.wysiwyg = function(options) {

		var self = $(this);
		$.ajax({
			dataType: 'json',
			url: 'admin/cms/wysiwyg/modules',
			success: function(enhancers) {
				options = $.extend({
					// Location of TinyMCE script
					script_url : '/static/cms/js/vendor/tinymce/tiny_mce_src.js',
					theme      : 'nos',
					plugins    : 'spellchecker,xhtmlxtras,style,table,advlist,inlinepopups,media,searchreplace,paste,noneditable,visualchars,nonbreaking',
					theme_nos_enhancers : enhancers
				}, options || {});

				$(self).tinymce(options);

			}
		});
    };
})(jQuery);