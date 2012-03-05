<?php
return array(
    // List view
    'display_list' => array(
        'order_by'    => array('blog_created_at' => 'DESC', 'blog_id' => 'DESC'),
    ),

    'display_list_main' => array(
        'list_view'   => 'front/list',
    ),

    'display_author' => array(
        'list_view'   => 'front/list_author',
    ),

    'display_category' => array(
        'list_view'   => 'front/list_category',
    ),

    'display_tag' => array(
        'list_view'   => 'front/list_tag',
    ),

    // Item view
    'display_list_item' => array(
        'fields'      => 'title author date categories thumbnail summary tags stats',
        'title_tag'   => 'h2',
        'item_view'   => 'front/item_list',
        'fields_view' => 'front/fields',
    ),

    'display_item' => array(
        'fields'      => 'title author date categories thumbnail summary tags stats wysiwyg',
        'title_tag'   => 'h1',
        'item_view'   => 'front/item',
        'fields_view' => 'front/fields',
    ),
);
?>
