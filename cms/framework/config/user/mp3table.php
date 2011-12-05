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
		'model' => 'Cms\Model_User',
		'related' => array('groups'),
	),
	'tab' => array(
		'label' => 'Users',
		'iconUrl' => 'static/cms/img/32/user.png',
	),
	'ui' => array(
		'label' => 'Users',
		'adds' => array(
			array(
				'label'   => 'Add a user',
				'iconUrl' => 'static/modules/cms_blog/img/16/blog.png',
				'url'     => 'admin/user/form/add',
			),
		),
		'grid' => array(
			'columns' => array(
				array(
					'headerText' => 'User',
					'dataKey' => 'fullname',
					'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
							args.$container.closest("td").attr("title", args.row.data.fullname);

							$("<a href=\"admin/user/form/edit/" + args.row.data.id + "\"></a>")
								.text(args.row.data.fullname)
								.appendTo(args.$container)
								.click(function(e) {
									$.nos.tabs.openInNewTab({
										url : this.href
									});
									e.preventDefault();
								});

							return true;
						}
					}',
				),
				 array(
					'headerText' => 'Email',
					'dataKey' => 'email',
				),
				array(
					'headerText' => 'Permissions',
					'allowSizing' => false,
					'width' => 1,
					'showFilter' => false,
					'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
							args.$container.css("text-align", "center");

							$("<a href=\"admin/user/group/permission/edit/" + args.row.data.id_permission + "\"></a>")
								.addClass("ui-state-default")
								.append("<img src=\"static/cms/img/icons/tick.png\" />")
								.appendTo(args.$container)
								.click(function() {
									$.nos.tabs.openInNewTab({
										url: this.href
									});
									return false;
								});

							return true;
						}
					}',
				),
			),
			'proxyurl' => 'admin/user/list/json',
		),
		'inspectors' => array(),
	),
	'dataset' => array(
		'id' => 'user_id',
		'fullname' => 'user_fullname',
		'email' => 'user_email',
		'id_permission' => function($object) {
			return reset($object->groups)->group_id ?: $object->user_id;
		}
	),
	'inputs' => array(),
);