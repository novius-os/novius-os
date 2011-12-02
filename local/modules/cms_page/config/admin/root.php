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
	'widget_id' => 'inspector-root',
	'input_name'   => 'rac_id',
	'urljson' => 'admin/cms_page/inspector/root/json',
	'query' => array(
		'model' => 'Cms\Page\Model_Root',
	),
	'dataset' => array(
		'id' => 'rac_id',
		'title' => 'rac_titre',
	),
	'columns' => array(
		array(
			'headerText' => 'Root',
			'dataKey' => 'title',
		),
	),
);