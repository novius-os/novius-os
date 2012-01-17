<?php

namespace Cms;

class Controller_Tinymce extends Controller_Generic_Admin {

	public function action_image() {

		\Asset::add_path('static/cms/js/jquery/wijmo/');
		\Asset::css('aristo/jquery-wijmo.css', array(), 'css');
		\Asset::css('jquery.wijmo-open.1.5.0.css', array(), 'css');

		\Asset::add_path('static/cms/');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('mystyle.css', array(), 'css');

		$this->template->body = \View::forge('cms::tinymce/image');
		return $this->template;
	}
}