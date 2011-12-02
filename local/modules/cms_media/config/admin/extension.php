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
	'widget_id' => 'inspector-extension',
	'input_name'   => 'media_extension[]',
	'columns' => array(
		array(
			'headerText' => 'Type of file',
			'dataKey' => 'title',
			'cellFormatter' => 'function(args) {
				if ($.isPlainObject(args.row.data)) {
					var text = "";
					if (args.row.data.icon) {
						text += "<img style=\"vertical-align:middle\" src=\"static/modules/cms_media/img/16/" + args.row.data.icon + "\"> ";
					}
					text += args.row.data.title;

					args.$container.html(text);

					return true;
				}
			}',
		),
		array(
			'visible' => false,
		),
		array(
			'visible' => false,
		),
	),
	'data' => array(
		array(
			'id' => 'image',
			'title' => 'Images',
			'icon' => 'image.png',
		),
		array(
			'id' => 'document',
			'title' => 'Documents',
			'icon' => 'document-office.png',
		),
		array(
			'id' => 'music',
			'title' => 'Music',
			'icon' => 'music-beam.png',
		),
		array(
			'id' => 'video',
			'title' => 'Videos',
			'icon' => 'film.png',
		),
		array(
			'id' => 'archive',
			'title' => 'Compressed archive',
			'icon' => 'folder-zipper.png',
		),
		array(
			'id' => 'text',
			'title' => 'Textual content',
			'icon' => 'document-text.png',
		),
		array(
			'id' => 'other',
			'title' => 'Other',
			'icon' => 'book-question.png',
		),
	),
);