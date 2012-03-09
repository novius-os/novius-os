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

class Controller_Admin_Media_Actions extends Controller_Extendable {


    protected static function  _get_media_with_permission($media_id, $permission) {
        if (empty($media_id)) {
            throw new \Exception('No media specified.');
        }
        $media = Model_Media_Media::find($media_id);
        if (empty($media)) {
            throw new \Exception('Media not found.');
        }
        if (!static::check_permission_action('delete', 'controller/admin/media/mp3grid/list', $media)) {
            throw new \Exception('Permission denied');
        }
        return $media;
    }

	public function action_delete_media($media_id = null) {
        try {
            $media = static::_get_media_with_permission($media_id, 'delete');
            return \View::forge('cms::admin/media/media_delete', array(
                'media'       => $media,
                'usage_count' => count($media->link),
            ));
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
            \Response::json($body);
		}
    }

	public function action_delete_media_confirm() {
        try {
            $media_id = \Input::post('id');
            // Allow GET for easier dev
            if (empty($media_id) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $media_id = \Input::get('id');
            }

            $media = static::_get_media_with_permission($media_id, 'delete');

            // Delete database & relations (link)
            $media->delete();
            // Delete file from the hard drive
            $media->delete_from_disk();
            // Delete cached entries (image thumbnails)
            $media->delete_public_cache();

			$body = array(
				'notify' => 'File successfully deleted.',
                'fireEvent' => array(
	                'event' => 'reload',
                    'target' => 'cms_media_media',
                ),

			);
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
    }

    /**
     *
     * @param   int     $folder_id ID of the folder
     * @param   string  $permission Which permission to check
     * @return  Cms\Model_Media_Folder
     */
    protected static function _get_folder_with_permission($folder_id, $permission) {
        if (empty($folder_id)) {
            throw new \Exception('No folder specified.');
        }
        $folder = Model_Media_Folder::find($folder_id);
        if (empty($folder)) {
            throw new \Exception('Folder not found.');
        }
        if (!static::check_permission_action($permission, 'controller/admin/media/inspector/folder', $folder)) {
            throw new \Exception('Permission denied');
        }
        return $folder;
    }

	public function action_delete_folder($folder_id = null) {
        try {
            $folder = static::_get_folder_with_permission($folder_id, 'delete');
            return \View::forge('cms::admin/media/folder_delete', array(
                'folder'      => $folder,
                'media_count' => $folder->count_media(),
            ));
            throw new \Exception($count);
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
            \Response::json($body);
		}
    }

	public function action_delete_folder_confirm() {
        try {
            $folder_id = \Input::post('id');
            // Allow GET for easier dev
            if (empty($folder_id) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $folder_id = \Input::get('id');
            }
            $folder = static::_get_folder_with_permission($folder_id, 'delete');

            $count_medias = $folder->count_media();
            // Basic check to prevent false supression
            if (!is_dir($folder->path()) && $count_medias > 0) {
                throw new \Exception(strtr('{count} medias were found, but folder was nonexistent.', array(
                    '{count}' => $count_medias,
                )));
            }

            // Strategy : try to delete the database records first, as we can sometimes (if supported) rollback with the transaction
            // Delete the files afterwards and commit the transaction if it's a success

            \DB::start_transaction();
            // find_children_recursive($include_self = true)
            $all_folders = $folder->find_children_recursive(true);
            $folder_ids = array_keys($all_folders);

            $escaped_path_ids = array();
            foreach($folder_ids as $id) {
                $escaped_path_ids[] = (int) $id;
            }
            // Cleanup empty values
            $escaped_path_ids = array_filter($escaped_path_ids);
            $escaped_path_ids = implode(',', $escaped_path_ids);

            $pk = Model_Media_Media::primary_key();
            $pk = $pk[0];
            $table_folder = Model_Media_Folder::table();
            $table_media  = Model_Media_Media::table();
            $table_link   = Model_Media_Link::table();

            // Delete linked medias
            \DB::query("
                DELETE $table_link.* FROM $table_link
                LEFT JOIN $table_media ON media_id = medil_media_id
                WHERE
                    media_path_id IN ($escaped_path_ids)")->execute();

            // Delete media entries
            \DB::query("
                DELETE $table_media.* FROM $table_media
                WHERE
                    media_path_id IN ($escaped_path_ids)")->execute();

            // Can throw an exception
            $folder->delete_from_disk();
            $folder->delete_public_cache();

            // Delete folder entries
            \DB::query("
                DELETE $table_folder.* FROM $table_folder
                WHERE
                    medif_id IN ($escaped_path_ids)")->execute();

            \DB::commit_transaction();
            $body = array(
                'notify' => 'Folder successfully deleted.',
				'fireEvent' => array(
					'event' => 'reload',
					'target' => array('cms_media_media', 'cms_media_folders'),
                ),
            );
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
		}
        \Response::json($body);
    }
}