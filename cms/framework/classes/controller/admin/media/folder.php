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

class Controller_Admin_Media_Folder extends Controller_Extendable {

	public function action_add($folder_id = null) {

        if (!static::check_permission_action('add', 'controller/admin/media/inspector/folder')) {
            \Response::json(array(
                'error' => __('Permission denied'),
            ));
        }
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
                    //'tag'  => 'button',
                    'class' => 'primary',
                    'value' => __('Add'),
                    'data-icon' => 'check',
                ),
            ),
        ));
		return \View::forge('cms::admin/media/folder_add', array(
            'fieldset' => $fieldset,
            'folder' => $folder,
            'hide_widget_media_path' => $hide_widget_media_path,
		), false);
	}

	public function action_edit($folder_id = null) {

		$folder = Model_Media_Folder::find($folder_id);
        $basename = pathinfo($folder->medif_path, PATHINFO_BASENAME);
        $fieldset = \Fieldset::build_from_config(array(
            'medif_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $folder->medif_id,
                ),
            ),
            'medif_title' => array(
                'form' => array(
                    'type' => 'text',
                    'value' => $folder->medif_title,
                ),
                'label' => __('Title: '),
            ),
            'medif_path' => array(
                'form' => array(
                    'type' => 'text',
                    'value' => $basename,
                ),
                'label' => __('SEO, folder URL:'),
            ),
            'save' => array(
                'form' => array(
                    'type' => 'submit',
                    //'tag'  => 'button',
                    'class' => 'primary',
                    'value' => __('Edit'),
                    'data-icon' => 'check',
                ),
            ),
        ));
		return \View::forge('cms::admin/media/folder_edit', array(
            'fieldset' => $fieldset,
            'folder' => $folder,
            'checked' => $basename == $folder::friendly_slug($folder->medif_title),
		), false);
	}

	public function action_do() {

		try {
            if (!static::check_permission_action('add', 'controller/admin/media/inspector/folder')) {
                throw new \Exception(__('Permission denied'));
            }
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
				'notify' => 'Sub-folder successfully created.',
				'closeDialog' => true,
				'fireEvent' => array(
					'event' => 'reload',
					'target' => 'cms_media_folders',
                ),
			);
		} catch (\Exception $e) {
			$body = array(
				'error' => $e->getMessage(),
			);
		}

		\Response::json($body);
	}

    public function action_do_edit() {
        try {
            if (!static::check_permission_action('add', 'controller/admin/media/inspector/folder')) {
                throw new \Exception(__('Permission denied'));
            }

            $folder = Model_Media_Folder::find(\Input::post('medif_id'));
            if (empty($folder)) {
                throw new \Exception('Folder not found.');
            }

            $folder->medif_title = \Input::post('medif_title');
            if (empty($folder->medif_title)) {
                throw new \Exception('Please provide a title.');
            }

            $old_folder = clone $folder;

            $path  = \Input::post('medif_path');

            if (empty($path) ) {
                $path = $folder->medif_title;
            }

            $path = Model_Media_Folder::friendly_slug($path, '-', true);
            if (empty($path)) {
                throw new \Exception(__('Generated slug was empty.'));
            }

            if (false === $folder->set_path($path)) {
                throw new \Exception(__("The parent folder doesn't exists."));
            }

            // Slug has changed
            if ($folder->path() != $old_folder->path()) {

                $duplicate_folder = Model_Media_Folder::find_by_medif_path($folder->medif_path);
                if (!empty($duplicate_folder)) {
                    throw new \Exception(__('A folder with the same name already exists.'));
                }

                if (\File::rename_dir($old_folder->path(), $folder->path())) {
                    // refresh_path($cascade_children = true, $cascade_media = true
                    $folder->refresh_path(true, true);
                } else {
                    // Restore old path if rename failed
                    $folder->medif_path = $old_folder->medif_path;
                }

                $old_folder->delete_public_cache();
            }
            $folder->save();

			$body = array(
				'notify' => 'Folder successfully edited.',
				'closeDialog' => true,
				'fireEvent' => array(
					'event' => 'reload',
					'target' => array('cms_media_media', 'cms_media_folders'),
                ),
			);

        } catch (\Exception $e) {
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