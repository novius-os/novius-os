<?php

namespace Cms;

class Controller_Tinymce extends Controller {

	public function action_image() {
		return \View::forge('cms::tinymce/image');
	}
}