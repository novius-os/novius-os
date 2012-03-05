<?php

namespace Cms;

class Controller_Tinymce extends Controller {

	public function action_image() {

		\Asset::add_path('static/cms/js/vendor/wijmo/');
		\Asset::css('aristo/jquery-wijmo.css', array(), 'css');
		\Asset::css('jquery.wijmo-complete.all.2.0.0.min.css', array(), 'css');

		\Asset::add_path('static/cms/');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('form.css', array(), 'css');

		return \View::forge('cms::tinymce/image');
	}
}