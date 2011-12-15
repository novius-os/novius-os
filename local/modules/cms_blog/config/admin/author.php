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
		'order_by' => 'user_fullname',
	),
	'columns' => array(
		array(
			'headerText' => 'Author',
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
            )
        ),
	),
	'dataset' => array(
		'id' => 'user_id',
		'title' => 'user_fullname',
	),
	'urljson' => 'admin/cms_blog/inspector/author/json',
	'input_name'   => 'blog_auteur_id[]',
	'widget_id' => 'inspector-author',
);