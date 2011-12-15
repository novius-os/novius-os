<?php

namespace Cms;

class Controller_Wysiwyg extends \Controller {

	public function action_modules() {

		\Config::load('app::front', true);
		$json = \Config::get('app::front', array());

		$response = \Response::forge(\Format::forge()->to_json($json), 200, array(
			'Content-Type' => 'application/json',
		));
		$response->send(true);
		exit();
	}
}