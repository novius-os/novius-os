<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
use Cms\I18n;

I18n::load('media', 'cms_media');

return array(
	'query' => array(
		'model' => 'Cms\Model_Media_Media',
		'related' => array(),
		'limit' => 10,
	),
	'search_text' => array(
		'media_title',
		'media_ext',
		'media_file',
	),
    'selectedView' => 'default',
    'views' => array(
        'default' => array(
            'name' => __('Default view'),
            'json' => array(
                'static/cms/js/admin/media/common.js',
                'static/cms/js/admin/media/media.js'
            ),
        ),
        'image_pick' => array(
            'name' => __('Image'),
            'virtual' => true,
            'json' => array(
                'static/cms/js/admin/media/common.js',
                'static/cms/js/admin/media/media.js',
                'static/cms/js/admin/media/image_pick.js'
            ),
        )
    ),
    'i18n' => array(
        'Media center' => __('Media center'),
        'Add a media' => __('Add a media'),
        'Add a folder' => __('Add a folder'),
        'Title' => __('Title'),
        'Ext.' => __('Ext.'),
        'Edit' => __('Edit'),
        'Delete' => __('Delete'),
        'Visualise' => __('Visualise'),
        'Pick' => __('Pick'),
        'Folder' => __('Folder'),
        'Folders' => __('Folders'),
        'Type of file' => __('Type of file'),
        'File name:' => __('File name:'),
        'Path:' => __('Path:'),
        'Upload a new file' => __('Upload a new file'),

        'addDropDown' => __('Select an action'),
        'columns' => __('Columns'),
        'showFiltersColumns' => __('Filters column header'),
        'visibility' => __('Visibility'),
        'settings' => __('Settings'),
        'vertical' => __('Vertical'),
        'horizontal' => __('Horizontal'),
        'hidden' => __('Hidden'),
        'item' => __('media'),
        'items' => __('medias'),
        'showNbItems' => __('Showing {{x}} medias out of {{y}}'),
        'showOneItem' => __('Show 1 media'),
        'showNoItem' => __('No media'),
        'showAll' => __('Show all medias'),
        'views' => __('Views'),
        'viewGrid' => __('Grid'),
        'viewThumbnails' => __('Thumbnails'),
        'preview' => __('Preview'),
        'loading' => __('Loading...'),
    ),
	'dataset' => array(
		'id' => 'media_id',
		'title' => 'media_title',
		'extension' => 'media_ext',
		'file_name' => 'media_file',
		'path' => function($object) {
            return $object->get_public_path();
        },
		'path_folder' => function($object) {
            return dirname($object->get_public_path());
        },
		'image' => function($object) {
            return $object->is_image();
        },
		'thumbnail' => function($object) {
            return $object->is_image() ? $object->get_public_path_resized(64, 64) : '';
        },
		'height' => 'media_height',
		'width' => 'media_width',
		'thumbnailAlternate' => function($object) {
			$extensions = array(
				'gif' => 'image.png',
				'png' => 'image.png',
				'jpg' => 'image.png',
				'jpeg' => 'image.png',
				'bmp' => 'image.png',
				'doc' => 'document.png',
				'xls' => 'document.png',
				'ppt' => 'document.png',
				'docx' => 'document.png',
				'xlsx' => 'document.png',
				'pptx' => 'document.png',
				'odt' => 'document.png',
				'odf' => 'document.png',
				'odp' => 'document.png',
				'pdf' => 'document.png',
				'mp3' => 'music.png',
				'wav' => 'music.png',
				'avi' => 'video.png',
				'mkv' => 'video.png',
				'mpg' => 'video.png',
				'mpeg' => 'video.png',
				'mov' => 'video.png',
				'zip' => 'archive.png',
				'rar' => 'archive.png',
				'tar' => 'archive.png',
				'gz' => 'archive.png',
				'7z' => 'archive.png',
				'txt' => 'text.png',
				'xml' => 'text.png',
				'htm' => 'text.png',
				'html' => 'text.png',
			);
			return isset($extensions[$object->media_ext]) ? 'static/cms/img/64/'.$extensions[$object->media_ext] : '';
		},
	),
	'inputs' => array(
		'folder_id' => function($value, $query) {
			if ($value) {
				$query->where(array('media_path_id', '=', $value));
				$query->order_by('media_title');
			}
			return $query;
		},
		'media_extension' => function($value, $query) {
			static $extensions = array(
				'image' => 'gif,png,jpg,jpeg,bmp',
				'document' => 'doc,xls,ppt,docx,xlsx,pptx,odt,odf,odp,pdf',
				'music' => 'mp3,wav',
				'video' => 'avi,mkv,mpg,mpeg,mov',
				'archive' => 'zip,rar,tar,gz,7z',
				'text' => 'txt,xml,htm,html',
			);
			$ext = array();
			$other = array();
			$value = (array) $value;
			foreach($extensions as $extension => $extension_list) {
				$extension_list = explode(',', $extension_list);
				if (in_array($extension, $value)) {
					$ext = array_merge($ext, $extension_list);
				} else {
					$other = array_merge($other, $extension_list);
				}
			}
			$opened = false;
			if (!empty($ext)) {
				$opened or $query->and_where_open();
				$opened = true;
				$query->or_where(array('media_ext', 'IN', $ext));
			}
			if (in_array('other', $value)) {
				$opened or $query->and_where_open();
				$opened = true;
				$query->or_where(array('media_ext', 'NOT IN', $other));
			}
			$opened and $query->and_where_close();

			$query->order_by('media_title');
			return $query;
		},
	),
);