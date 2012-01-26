<?php

namespace Cms;

class Controller_Wysiwyg extends \Controller {

	public function action_modules() {

		\Config::load('app::front', true);
		$json = \Config::get('app::front', array());

		\Response::json($json);
	}
}