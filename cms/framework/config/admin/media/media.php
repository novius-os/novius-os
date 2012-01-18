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
		'model' => 'Cms\Model_Media_Media',
		'related' => array(),
		'limit' => 10,
	),
	'tab' => array(
		'label' => 'Media centre',
		'iconUrl' => 'static/cms/img/32/media.png',
	),
	'ui' => array(
		'label' => 'Media',
        'texts' => array(
            'items' => 'media',
            'item' => 'Media'
        ),
		'adds' => array(
			array(
				'label' => 'Add a media',
				'url' => 'admin/admin/media/add',
			),
			array(
				'label' => 'Add a folder',
				'iconClasses' => 'nos-icon16 nos-icon16-folder',
				'url' => 'admin/admin/media/folder/add',
			),
		),
		'grid' => array(
			'id' => 'cms_media',
			'proxyurl' => 'admin/admin/media/list/json',
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
				'actions',
			),
		),
		'actions' => array(
			array(
				'label' => 'Edit',
				'action' => 'function(item) {
					$.nos.tabs.openInNewTab({
						url : "admin/admin/media/form?id=" + item.id,
						label : item.title
					});
				}',
			),
			array(
				'label' => 'Delete',
				'action'   =>  'function(item) {
					if (confirm("Are you sure ?")) {
						$.nos.tabs.openInNewTab({
							url : "admin/admin/media/form?id=" + item.id,
							label : item.title
						});
					}
				}',
			),
			array(
				'label' => 'Visualize',
				'action' => 'function(item) {
					window.open(item.image);
				}',
			),
		),
		'thumbnails' => array(
			'dataParser' => "function(size, item) {
				var data = {
					title : item.title,
					thumbnail : (item.image ? item.thumbnail : item.thumbnailAlternate).replace(/64/g, size),
					thumbnailAlternate : (item.image ? item.thumbnailAlternate : '').replace(/64/g, size),
					actions : []
				};
				return data;
			}",
		),
		'defaultView' => 'thumbnails',
		'preview' => array(
			'hide' => false,
			'vertical' => true,
			'options' => array(
				'dataParser' => "function(item) {
					var data = {
						title : item.title,
						thumbnail : (item.image ? item.thumbnail.replace(/64/g, 256) : item.thumbnailAlternate),
						thumbnailAlternate : (item.image ? item.thumbnailAlternate : ''),
						meta : [
							{
								label : 'Id',
								value : item.id
							},
							{
								label : 'Extension',
								value : item.extension
							},
							{
								label : 'File name',
								value : item.file_name
							},
							{
								label : 'Path',
								value : item.path
							}
						],
						actions : [
							{
								label : 'Edit',
								action : function() {
									$.nos.tabs.openInNewTab({
										url : 'admin/admin/media/form?id=' + item.id,
										label : item.title
									});
								}
							},
							{
								label : 'Delete',
								action : function() {
									if (confirm('Are you sure ?')) {
										$.nos.tabs.openInNewTab({
											url : 'admin/admin/media/form?id=' + item.id,
											label : item.title
										});
									}
								}
							},
							{
								label : 'Visualize',
								button : true,
								action : function() {
									window.open(item.image);
								}
							},
						]
					};
					return data;
				}",
			)
		),
		'inspectors' => array(
			array(
				'vertical' => true,
				'label' => 'Folders',
				'url' => 'admin/admin/media/inspector/folder/list',
				'widget_id' => 'inspector-folder',
			),
			array(
				'widget_id' => 'inspector-extension',
				'label' => 'Type of file',
				'url' => 'admin/admin/media/inspector/extension/list',
			),
		),
	),
	'dataset' => array(
		'id' => 'media_id',
		'title' => 'media_title',
		'extension' => 'media_ext',
		'file_name' => 'media_file',
		'path' => function($object) {
            return $object->get_public_path();
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
			return $extensions[$object->media_ext] ? 'static/cms/img/64/'.$extensions[$object->media_ext] : '';
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