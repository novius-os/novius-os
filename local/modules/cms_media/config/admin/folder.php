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
	'widget_id' => 'inspector-folder',
	'input_name'   => 'folder_id',
	'urljson' => 'admin/cms_media/inspector/folder/json',
	'query' => array(
		'model' => 'Cms\Media\Model_Folder',
	),
	'columns' => array(
		array(
			'headerText' => 'Folder name',
			'dataKey' => 'title',
		),
		array(
			'headerText' => 'Add',
			'cellFormatter' => 'function(args) {
				if ($.isPlainObject(args.row.data)) {
					args.$container.css("text-align", "center");

					$("<a href=\"admin/cms_media/upload/form/" + args.row.data.id + "\"></a>")
						.addClass("ui-state-default")
						.append("<span class=\"ui-icon ui-icon-transferthick-e-w\"></span>")
						.appendTo(args.$container)
						.click(function(e) {
							$.nos.dialog({
								contentUrl: this.href,
								title: "Upload a new file in the \"" + args.row.data.title + "\" folder",
								width: 400,
								height: 200,
							});
							e.preventDefault();
						});

					$("<a href=\"admin/cms_media/folder/form/" + args.row.data.id + "\"></a>")
						.addClass("ui-state-default")
						.append("<span class=\"ui-icon ui-icon-folder-collapsed\"></span>")
						.appendTo(args.$container)
						.click(function(e) {
							$.nos.dialog({
								contentUrl: this.href,
								title: "Create a sub-folder in \"" + args.row.data.title + "\"",
								width: 550,
								height: 200,
							});
							e.preventDefault();
						});

					return true;
				}
			}',
			'allowSizing' => false,
			'width' => 1,
			'showFilter' => false,
		),
	),
	'dataset' => array(
		'id' => 'medif_id',
		'title' => 'medif_title',
	),
);