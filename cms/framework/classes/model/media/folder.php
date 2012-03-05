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

use Fuel\Core\Uri;

class Model_Media_Folder extends \Cms\Orm\Model {
    protected static $_table_name = 'os_media_folder';
    protected static $_primary_key = array('medif_id');

	protected static $_has_many = array(
		'children' => array(
			'key_from'       => 'medif_id',
			'model_to'       => '\Cms\Model_Media_Folder',
			'key_to'         => 'medif_parent_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
        'media' => array(
			'key_from'       => 'medif_id',
			'model_to'       => '\Cms\Model_Media_Media',
			'key_to'         => 'media_path_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'medif_parent_id',
			'model_to'       => '\Cms\Model_Media_Folder',
			'key_to'         => 'medif_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);


    /**
     * Delete all the public/cache entries (image thumbnails) for this folder
     *
     * @return void
     */
    public function delete_public_cache() {

        // Delete cached media entries
        $path_public     = DOCROOT.Model_Media_Media::$public_path.$this->medif_path;
        $path_thumbnails = str_replace(DOCROOT.'media/', DOCROOT.'cache/media/', $path_public);
        try {
            // delete_dir($path, $recursive, $delete_top)
            is_dir($path_public)     and \File::delete_dir($path_public,     true, true);
            is_dir($path_thumbnails) and \File::delete_dir($path_thumbnails, true, true);
            return true;
        } catch (\Exception $e) {
            if (\Fuel::$env == \Fuel::DEVELOPMENT) {
                throw $e;
            }
        }
    }

    public function delete_from_disk() {

        $path = $this->path();
        if (is_dir($path)) {
            // delete_dir($path, $recursive, $delete_top)
            return \File::delete_dir($path, true, true);
        }
        return true;
    }

    public function path($file = '') {
        return APPPATH.'media/'.$this->medif_path.$file;
    }

    /**
     * Get the list of all IDs of the children
     *
     * @param bool $include_self
     * @return array
     */
    public function get_ids_children($include_self = true) {
        $ids = array();
        if ($include_self) {
            $ids[] = $this->medif_id;
        }
        $this->_populate_id_children($this, $ids);
        return $ids;
    }

    public function find_children_recursive($include_self = true) {

        // This is weird, but it doesn't work when called directly...
        return static::search(array(array('medif_id', 'IN', $this->get_ids_children($include_self))));
    }

    protected static function _populate_id_children($current_folder, &$array) {
        foreach ($current_folder->children as $child) {
            $array[] = $child->medif_id;
            static::_populate_id_children($child, $array);
        }

    }

    public function count_media() {
        /// get_ids_children($include_è_self)
        $folder_ids = $this->get_ids_children(true);
        return Model_Media_Media::count(array(
            'where' => array(
                array('media_path_id', 'IN', $folder_ids),
            ),
        ));
    }

    public function count_media_usage() {
        $folder_ids = $this->get_ids_children(true);
        return Model_Media_Link::count(array(
            'related' => array('media'),
            'where' => array(
                array('media.media_path_id', 'IN', $folder_ids),
            ),
        ));
    }

    public static function friendly_slug($slug, $sep = '-', $lowercase = true) {

        $slug = strtr($slug, '@€', 'ae');
		$slug = \Inflector::ascii($slug);

        if ($lowercase) {
            $slug = \Str::lower($slug);
        }

        $quoted_sep = preg_quote($sep);
        $slug = preg_replace("`[\s+]`", $sep, $slug);
        $slug = preg_replace("`[^\w$quoted_sep]`i", '', $slug);
        $slug = preg_replace("`$quoted_sep+`", $sep, $slug);
        $slug = trim($slug, $sep);

        return $slug;
    }

    public function check_and_filter_slug($sep = '-', $lowercase = true) {

        $exploded_path = explode('/', trim($this->medif_path, '/'));
        $path = '';
        foreach ($exploded_path as $part) {
            $path .= static::friendly_slug($part, '-', true).'/';
        }

        // empty or "/"
        if (strlen($path) <= 1) {
            return false;
        }
		$this->medif_path = $path;

        return true;
    }

    public function set_path($path) {

		$parent = $this->parent;
        if (empty($parent)) {
            return false;
        }
        $this->medif_path = $parent->medif_path.$path.'/';
        return true;
    }

	public function refresh_path($cascade_children = true, $cascade_media = true) {
        $current_path = pathinfo($this->medif_path, PATHINFO_BASENAME);
        $this->set_path($current_path);
        if ($cascade_children) {
            foreach ($this->children as $child) {
                $child->refresh_path(true, $cascade_media);
                $child->save();
            }
        }
        if ($cascade_media) {
            // 1 request for each updated folder
            \DB::update(Model_Media_Media::table())
                ->value('media_path', $this->medif_path)
                ->where('media_path_id', $this->medif_id)
                ->execute();
        }
    }

    /**
     * Properties
     * medif_id
     * medif_parent_id
     * medif_path
     * medif_title
     */
}
