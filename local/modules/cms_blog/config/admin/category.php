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
		'model' => 'Cms\Blog\Model_Category',
	),
	'columns' => array(
		array(
			'headerText' => 'Category',
			'dataKey' => 'title',
		),
        array(
            'actions' => array(
                array(
                    'icon'      => 'ui-icon ui-icon-pencil',
                    'action'   =>  'function(args) {
                                                $.nos.tabs.openInNewTab({
                                                    url     : "admin/cms_blog/form?id=" + args.row.data.id,
                                                    label   : "Update"
                                                });
                                            }',
                    'label'     => 'Update',
                ),
                array(
                    'icon'  => 'ui-icon ui-icon-close',
                    'action'   =>  'function(args) {
                                                alert("La suppression d\'un élément n\'est pas encore implémentée !\n Id de l\'élément : " + args.row.data.id);
                                            }',
                    'label' => 'Delete',
                ),
            )
        ),
	),
	'dataset' => array(
		'id'    => 'blgc_id',
		'title' => 'blgc_titre',
	),
	'urljson' => 'admin/cms_blog/inspector/category/json',
	'input_name'   => 'blgc_id[]',
	'widget_id' => 'inspector-category',
);