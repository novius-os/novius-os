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
		'model' => 'Cms\Model_Page_Page',
		'related' => array(),
	),
	'tab' => array(
		'label' => 'Pages',
		'iconUrl' => 'static/cms/img/32/page.png',
	),
	'ui' => array(
		'label' => 'Pages',
		'adds' => array(
			array(
				'label' => 'Add a Page',
				'url' => 'admin/admin/page/page/add',
			),
			array(
				'label' => 'Add a root',
				'iconClasses' => 'nos-icon16 nos-icon16-root',
				'url' => 'admin/admin/page/root/add',
			),
		),
		'grid' => array(
			'proxyurl' => 'admin/admin/page/list/json',
			'columns' => array(
				array(
					'headerText' => 'Title 2',
					'dataKey' => 'title',
					'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
							args.$container.closest("td").attr("title", args.row.data.title);

							$("<a href=\"admin/admin/page/form/edit/" + args.row.data.id + "\"></a>")
								.text(args.row.data.title)
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
				//'lang',
			),
		),
		'inspectors' => array(
			array(
				'widget_id' => 'inspector-root',
				'vertical' => true,
				'label' => 'Roots',
				'iconClasses' => 'nos-icon16 nos-icon16-root',
				'url' => 'admin/admin/page/inspector/root/list',
			),
			array(
				'widget_id' => 'inspector-tree',
				'vertical' => true,
				'label' => 'Directories',
				'iconClasses' => 'nos-icon16 nos-icon16-root',
				'url' => 'admin/admin/page/inspector/tree/list',
			),
		),
	),
	'dataset' => array(
		'id' => 'page_id',
		'title' => 'page_titre',
	),
	'inputs' => array(							
		'rac_id' => function($value, $query) {
			if ($value) {
				$query->where(array('page_rac_id', '=', $value));
				//$query->where(array('page_niveau', '=', 1));
				$query->order_by('page_titre');
			}
			return $query;
		},
		'directory_id' => function($value, $query) {
			$query->where(array('page_niveau', '>', 0));
			$query->where(array('page_type', '!=', Cms\Model_Page_Page::TYPE_FOLDER));
			if ($value) {
				$query->where(array('page_pere_id', '=', $value));
			}
			return $query;
		},
	),
);