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

class Controller_Admin_Media_Folder extends \Controller {

	public function action_form($id) {

        $fieldset = \Fieldset::build_from_config(array(
            'medif_parent_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $folder->medif_id,
                ),
            ),
            'medif_title' => array(
                'form' => array(
                    'type' => 'text',
                ),
                'label' => __('Title: '),
            ),
            'medif_path' => array(
                'form' => array(
                    'type' => 'text',
                ),
                'label' => __('Path: ').$folder->medif_path.' ',
            ),
            'save' => array(
                'form' => array(
                    'type' => 'submit',
                    'class' => 'primary',
                    'value' => __('Add'),
                    'data-icon' => 'circle-plus',
                ),
            ),
        ));
		return \View::forge('cms::admin/media/folder/form', array(
            'fieldset' => $fieldset,
		), false);
	}

	public function action_do() {

		$path  = \Input::post('medif_path', '');
		$title = \Input::post('medif_title');

		if (empty($path) && !empty($title)) {
			$path = $title;
		}
		if (empty($title) && !empty($path)) {
			$title = \Inflector::humanize($path);
		}

		$path = \Inflector::ascii($path);
		$path = trim($path);
		$path = trim($path, '/\\');

		try {
			if (empty($path) || empty($title)) {
				throw new \Exception('Please provide a title or a path.');
			}

			$folder = new Model_Media_Folder();
			$folder->medif_parent_id = \Input::post('medif_parent_id');

			$parent = Model_Media_Folder::find($folder->medif_parent_id);
			$folder->medif_path  = $parent->medif_path.$path.'/';
			$folder->medif_title = $title;

			$folder->save();
			$body = array(
				'notify' => 'Sub-directory created successfully.',
				'closeDialog' => true,
				'listener_fire' => 'inspector-folder.refresh!',
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