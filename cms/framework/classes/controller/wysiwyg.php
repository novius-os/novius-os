<?php

namespace Cms;

class Controller_Wysiwyg extends \Controller {

	public function action_modules() {

		$json = array(
			array(
				'title' => 'Module de test',
				'id'    => 'test',
				'desc'  => 'Module de test',
			),
			array(
				'title' => 'Deuxième module pour test',
				'id'    => 'test_2',
				'desc'  => 'Module de test',
			),
		);

		$response = \Response::forge(\Format::forge()->to_json($json), 200, array(
			'Content-Type' => 'application/json',
		));
		$response->send(true);
		exit();
	}
}