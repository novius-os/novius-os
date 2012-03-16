<?php

namespace Cms;

class Controller_Admin_Wysiwyg extends \Controller {

	public function action_image() {
		return \View::forge('cms::tinymce/image');
	}

	public function action_modules() {

        \Config::load(APPPATH.'data'.DS.'config'.DS.'wysiwyg_enhancers.php', 'wysiwyg_enhancers');
        $functions = \Config::get('wysiwyg_enhancers', array());

		\Response::json($functions);
	}
}