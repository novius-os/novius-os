
(function($) {
    var inc = 1;
    $.fn.wysiwyg = function(options) {
        options = $.extend({
			// Location of TinyMCE script
			script_url : '/static/cms/js/jquery/tinymce/tiny_mce_src.js',
			theme      : 'nos',
			plugins    : 'template,xhtmlxtras,style,layer,table,save,advhr,advlist,inlinepopups,media,searchreplace,paste,noneditable,visualchars,contextmenu',
		}, options || {});
		
		$(this).tinymce(options);
    };
})(jQuery);