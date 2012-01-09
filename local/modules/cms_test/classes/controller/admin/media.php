<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Test;

class Controller_Admin_Media extends \Cms\Controller_Noviusos_Noviusos
{
	public function action_index()
	{
		\Fuel::add_module('cms_media');
		\Fuel::add_module('cms_blog');

		//*
		$blog = \Cms\Blog\Model_Blog::find(565);

		//$blog->{'media->vignette'};
		//$blog->media->vignette = 2;

		// Creates a media using __set() or the provider
		//$blog->{'media->test->medil_media_id'} = 1;
		//$blog->media->test2 = 2;

		//$blog->{'wysiwyg->test->wysiwyg_text'} = "AZE";
		//$blog->wysiwyg->test2 = "QSD";

		//\Debug::dump($blog->{'wysiwyg->test->wysiwyg_text'});
		//\Debug::dump($blog->wysiwyg->test2);

		//$blog->save();
		//exit();


		//*/
		//\Debug::dump($blog->media->vignette);

		$this->template->body = \View::forge('cms_test::media');
		return $this->template;
	}

	public function action_picker() {
		return \Request::forge('cms_test/admin/list')->execute()->response();
	}
}