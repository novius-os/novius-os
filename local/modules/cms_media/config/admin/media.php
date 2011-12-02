<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array(
	'query' => array(
		'model' => 'Cms\Media\Model_Media',
		'related' => array(),
	),
	'tab' => array(
		'label' => 'Media centre',
		'iconUrl' => 'static/modules/cms_media/img/32/media.png',
	),
	'ui' => array(
		'label' => 'Medias',
		'adds' => array(
			array(
				'label' => 'Add a media',
				//'iconClasses' => '',
				'url' => 'admin/cms_media/media/add',
			),
			array(
				'label' => 'Add a folder',
				'iconClasses' => 'cms_media-icon16 cms_media-icon16-folder',
				'url' => 'admin/cms_media/folder/add',
			),
		),
		'grid' => array(
			'columns' => array(
				array(
					'headerText' => 'Ext.',
					'dataKey' => 'extension',
					'width' => 1,
					'allowSizing' => false,

				),
				array(
					'headerText' => 'Title',
					'dataKey' => 'title',
				),
				//'lang',
			),
			'proxyurl' => 'admin/cms_media/list/json',
		),
		'inspectors' => array(
			array(
				'vertical' => true,
				'label' => 'Folders',
				'iconClasses' => 'cms_media-icon16 cms_media-icon16-folder',
				'url' => 'admin/cms_media/inspector/folder/list',
				'widget_id' => 'inspector-folder',
			),
			array(
				'widget_id' => 'inspector-extension',
				'label' => 'Type of file',
				'iconClasses' => 'cms_media-icon16 cms_media-icon16-folder',
				'url' => 'admin/cms_media/inspector/extension/list',
			),
		),
	),
	'dataset' => array(
		'id' => 'media_id',
		'title' => 'media_title',
		'extension' => 'media_ext',
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