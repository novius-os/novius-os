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
	'models' => array(
		array(
			'model' => 'Cms\Model_Media_Folder',
			'order_by' => 'medif_title',
			'childs' => array('Cms\Model_Media_Folder'),
			'dataset' => array(
				'id' => 'medif_id',
				'title' => 'medif_title',
			),
		),
	),
	'roots' => array(
		array(
			'model' => 'Cms\Model_Media_Folder',
			'where' => array(array('medif_parent_id', 'IS', \DB::expr('NULL'))),
			'order_by' => 'medif_title',
		),
	),
);