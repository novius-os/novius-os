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

	public function action_form($folder_id = null) {

        // Find root folder ID
        if (!$folder_id) {
            $query = Model_Media_Folder::find();
            $query->where(array('medif_parent_id' => null));
            $root = $query->get_one();
            $folder_id = $root->medif_id;
            $hide_widget_media_path = false;
        } else {
            $hide_widget_media_path = true;
        }

		$folder = Model_Media_Folder::find($folder_id);

        $fieldset = \Fieldset::build_from_config(array(
            'medif_parent_id' => array(
                'widget' => $hide_widget_media_path ? null : 'media_folder',
                'form' => array(
                    'type'  => 'hidden',
                    'value' => $folder->medif_id,
                ),
                'label' => __('Choose a folder where to put your media:'),
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
                'label' => __('SEO, folder URL:'),
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
            'folder' => $folder,
            'hide_widget_media_path' => $hide_widget_media_path,
		), false);
	}

	public function action_do() {

		try {
			$folder = new Model_Media_Folder();

            $folder->medif_title = \Input::post('medif_title');
            if (empty($folder->medif_title)) {
                throw new \Exception('Please provide a title or a path.');
            }

            $path  = \Input::post('medif_path');

            if (empty($path) ) {
                $path = $folder->medif_title;
            }

            $path = Model_Media_Folder::friendly_slug($path, '-', true);
            if (empty($path)) {
                throw new \Exception(__('Generated slug was empty.'));
            }

			$folder->medif_parent_id = \Input::post('medif_parent_id', 1);

            if (false === $folder->set_path($path)) {
                throw new \Exception(__("The parent folder doesn't exists."));
            }

            $duplicate_folder = Model_Media_Folder::find_by_medif_path($folder->medif_path);
            if (!empty($duplicate_folder)) {
                throw new \Exception(__('A folder with the same name already exists.'));
            }

			$folder->save();
			$body = array(
				'notify' => 'Sub-directory successfully created.',
				'closeDialog' => true,
				'listener_fire' => 'cms_media_folders.reload',
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