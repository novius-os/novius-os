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
	'widget_id' => 'inspector-tree',
	'input_name'   => 'directory_id',
	'urljson' => 'admin/admin/page/inspector/tree/json',
	'query' => array(
		'model' => 'Cms\Model_Page_Page',
	),
	'dataset' => array(
		'id' => 'page_id',
		'title' => 'page_titre',
	),
	'columns' => array(
		array(
			'headerText' => 'Directory',
			'dataKey' => 'title',
		),
	),
);