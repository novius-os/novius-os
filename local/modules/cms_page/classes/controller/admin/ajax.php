<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Page;

use Fuel\Core\Config;

class Controller_Admin_Ajax extends \Controller {

	public function before() {
		Config::load('templates', true);
		parent::before();
	}

	public function action_wysiwyg($page_id) {
		$id = $_GET['template_id'];
		$data = \Config::get('templates.id-'.$id, array());
		$data['layout'] = (array) $data['layout'];

		\Fuel::add_module('cms_media');

		$page = Model_Page::find($page_id);
		foreach ($data['layout'] as $wysiwyg => $coords)
		{
			$text = $page->wysiwyg->{$wysiwyg};
			preg_match_all('`src="nos://media/(\d+)(?:/(\d+)/(\d+))?"`', $text, $matches);
			$ids      = array();
			$replaces = array();
			foreach ($matches[1] as $id)
			{
				$ids[] = $id;
			}
			$medias = \Cms\Media\Model_Media::find($ids);
			foreach ($matches[1] as $k => $id)
			{
				$media = \Cms\Media\Model_Media::find($id);
				list($width, $height) = array($matches[2][$k], $matches[3][$k]);
				if ($width && $height && ($width != $media->media_width || $height != $media->media_height))
				{
					$replaces[$matches[0][$k]] = 'src="'.\Uri::base(true).$media->get_public_path_resized($width, $height).'" width="'.$width.'" height="'.$height.'" data-media-id="'.$id.'"';
				}
				else
				{
					$replaces[$matches[0][$k]] = 'src="'.\Uri::base(true).$media->get_public_path().'" data-media-id="'.$id.'"';
				}
			}
			$data['content'][$wysiwyg] = strtr($text, $replaces);
		}

		// @todo replace images
		// src="nos://media/ID" => src="http://real/url/here" data-media-id="ID"

		$response = \Response::forge(\Format::forge()->to_json($data), 200, array(
			'Content-Type' => 'application/json',
		));
		$response->send(true);
		exit();
	}
}