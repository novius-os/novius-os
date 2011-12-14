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
		'model' => 'Cms\Blog\Model_Blog',
		'related' => array('author'),
		'limit' => 20,
	),
	'tab' => array(
		'label' => 'Blog',
		'iconUrl' => 'static/modules/cms_blog/img/32/blog.png',
	),
	'ui' => array(
		'label' => 'Blog',
		'iconClasses' => 'cms_blog-icon16 cms_blog-icon16-blog',
		'adds' => array(
			array(
				'label' => 'Add a post',
				'url' => 'admin/cms_blog/blogform',
			),
			array(
				'label' => 'Add a category',
				'url' => 'admin/cms_blog/categoryform',
			),
		),
		'grid' => array(
			'columns' => array(
				array(
					'headerText' => 'Title',
					'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
							args.$container.closest("td").attr("title", args.row.data.title);

							$("<a href=\"admin/cms_blog/form?id=" + args.row.data.id + "\"></a>")
								.text(args.row.data.title)
								.appendTo(args.$container);

							return true;
						}
					}',
					'dataKey' => 'title',
				),
				'lang',
				array(
					'headerText' => 'Author',
					'dataKey' => 'author',
				),
				array(
					'headerText' => 'Date',
					'dataKey' => 'date',
                    'dataFormatString'  => 'MM/dd/yyyy HH:mm:ss',
                    'showFilter' => false,
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
                                                $.nos.ajax({
                                                    url: "admin/cms_blog/list/delete/" + args.row.data.id,
                                                    data: {},
                                                    success: function(response) {
                                                        if (response.success) {
                                                            $.nos.notify("Suppression réalisée !");
                                                            $("#mp3grid").mp3grid("gridRefresh");
                                                        } else {
                                                            $.nos.notify("Erreur lors de la suppression !", "error");
                                                        }
                                                    }
                                                });
                                            }',
                            'label' => 'Delete',
                        ),
                    )
                ),
			),
			'proxyurl' => 	'admin/cms_blog/list/json',
		),
		'inspectors' => array(
			array(
				'widget_id' => 'inspector-category',
				'label' => 'Categories',
				'vertical' => true,
				'url' => 'admin/cms_blog/inspector/category/list',
			),
			array(
				'widget_id' => 'inspector-tag',
				'hide' => true,
				'label' => 'Tags',
				'url' => 'admin/cms_blog/inspector/tag/list',
			),
			array(
				'widget_id' => 'inspector-author',
				'label' => 'Authors',
				'url' => 'admin/cms_blog/inspector/author/list',
			),
			array(
				'widget_id' => 'inspector-publishdate',
				'vertical' => true,
				'label' => 'Publish date',
				'url' => 'admin/cms_blog/inspector/date/list',
			),
			array(
				'widget_id' => 'inspector-lang',
				'vertical' => true,
				'label' => 'Language',
				'url' => 'admin/cms_blog/inspector/lang/list',
				'languages' => array(
					'fr' => 'Français',
					'en' => 'Anglais',
				),
			),
		),
	),
	'dataset' => array(
        'id' => 'blog_id',
		'title' => 'blog_titre',
        'lang' => 'blog_lang',
		'author' => array(
            'search_relation' => 'author',
            'search_column'   => 'author.user_fullname',
            'value' =>  function($object) {
                            return $object->author->user_fullname;
                        },
        ),
		'date' => array(
            'search_column'    =>  'blog_date_creation',
            'dataType'         => 'datetime',
            'value'             => function($object) {
                            return \Date::create_from_string($object->blog_date_creation, 'mysql')->format('%m/%d/%Y %H:%M:%S'); //%m/%d/%Y %H:%i:%s
                        },

        ),
	),
	'inputs' => array(
		'blgc_id' => function($value, $query) {
			if ( is_array($value) && count($value) && $value[0]) {
				$query->related('categories');
				$query->where(array('categories.blgc_id', 'in', $value));
			}
			return $query;
		},
		'tag_id' => function($value, $query) {
			if ( is_array($value) && count($value) && $value[0]) {
				$query->related('tags', array(
					'where' => array(
						array('tags.tag_id', 'in', $value),
					),
				));
			}
			return $query;
		},
		'blog_auteur_id' => function($value, $query) {
			if ( is_array($value) && count($value) && $value[0]) {
				$query->where(array('blog_auteur_id', 'in', $value));
			}
			return $query;
		},
		'blog_date_creation' => function($value, $query) {
			list($begin, $end) = explode('|', $value);
			if ($begin) {
				if ($begin = Date::create_from_string($begin, '%Y-%m-%d')) {
					$query->where(array('blog_date_creation', '>=', $begin->format('mysql')));
				}
			}
			if ($end) {
				if ($end = Date::create_from_string($end, '%Y-%m-%d')) {
					$query->where(array('blog_date_creation', '<=', $end->format('mysql')));
				}
			}
			return $query;
		},
	),
);