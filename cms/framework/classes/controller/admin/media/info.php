<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Media;

class Controller_Admin_Info extends \Cms\Controller_Noviusos_Noviusos {

	public function action_media($id)
	{
		$media = Model_Media::find($id);
		
		if ($media) {
			\Config::load('cms_media::admin/media', true);
			$dataset = \Config::get('cms_media::admin/media.dataset', array());
			$item = array();
			foreach ($dataset as $key => $data)
			{
				$item[$key] = is_callable($data) ? $data($media) : $media->$data;
			}
		} else {
			$item = null;
		}

		\Response::forge(\Format::forge()->to_json($item))->send(true);
		exit();
	}
}
