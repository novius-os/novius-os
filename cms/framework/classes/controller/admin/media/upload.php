<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

class Controller_Admin_Media_Upload extends Controller_Noviusos_Noviusos {

	public function action_form($id = null) {

        if (!$id) {
            $query = Model_Media_Folder::find();
            $query->where(array('medif_parent_id' => null));
            $root = $query->get_one();
            $id = $root->medif_id;
        }

		$folder = Model_Media_Folder::find($id);
		$this->template->body = \View::forge('cms::admin/media/upload/form', array(
			'folder' => $folder,
		));
		return $this->template;
	}

	public function action_do() {

		$media = new Model_Media_Media();

		$media->media_path_id = \Input::post('media_path_id');
		$media->media_file    = $_FILES['media']['name'];
		$media->media_module  = \Input::post('media_module', null);

		$media->media_title   = \Input::post('media_title', '');
		if (empty($media->media_title)) {
			$media->media_title = static::pretty_filename($media->media_file);
		}
		try {
			if (!is_uploaded_file($_FILES['media']['tmp_name'])) {
				throw new \Exception('Upload error');
			}
			$media->refresh_path();
			$dest = APPPATH.$media->get_public_path();
			if (is_file($dest)) {
				throw new \Exception('A file with the same name already exists.');
			}

			$disallowed_extensions = \Config::get('upload.disabled_extensions', array('php'));

			$ext = strtolower(pathinfo($dest, PATHINFO_EXTENSION));
			if (in_array($ext, $disallowed_extensions)) {
				throw new \Exception('This extension is disallowed for security reasons.');
			}

			$dest_dir = dirname($dest);
			is_dir($dest_dir) || mkdir($dest_dir);
			move_uploaded_file($_FILES['media']['tmp_name'], $dest);
			$media->save();
			$body = array(
				'notify' => 'File added successfully',
				'closeDialog' => true,
				'listener_fire' => 'refresh.cms_media_media',
				'listener_bubble' => true,
			);
		} catch (\Exception $e) {
			$dest && @unlink($dest);
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
	}

	protected static function pretty_filename($file) {
		$file = substr($file, 0, strrpos($file, '.'));
		$file = preg_replace('`[\W_-]+`', ' ', $file);
		$file = \Inflector::humanize($file, ' ');
		return $file;
	}
}