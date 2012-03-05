<?php
return array(
    'views' => array(
        'edit' => 'cms_blog::form/edit',
    ),
    'fields' => function() {
        return array (
            'blog_id' => array (
                'label' => 'Id: ',
                'widget' => 'text',
                'editable' => false,
            ),
            'blog_publication_start' => array (
                'label' => 'Published',
                'form' => array(
                    'type' => 'checkbox',
                    'value' => isset($object) && $object->blog_publication_start ? $object->blog_publication_start : \Date::forge(strtotime('now'))->format('mysql'),
                ),
            ),
            'blog_title' => array (
                'label' => 'Title: ',
                'form' => array(
                    'type' => 'text',
                ),
            ),
            'blog_author' => array(
                'label' => 'Alias: ',
                'form' => array(
                    'type' => 'text',
                ),
            ),
            'author->user_fullname' => array(
                'label' => 'Author: ',
                'widget' => 'text',
                'editable' => false,
            ),
            'wysiwygs->content->wysiwyg_text' => array(
                'label' => 'Contenu',
                'widget' => 'wysiwyg',
                'form' => array(
                    'style' => 'width: 100%; height: 500px;',
                    ),
            ),
            'medias->thumbnail->medil_media_id' => array(
                'label' => '',
                'widget' => 'media',
                'form' => array(
                    'title' => 'Thumbnail',
                ),
            ),
            'blog_created_at' => array(
                'label' => 'Created at:',
                'widget' => 'date_picker',
            ),
            'blog_read' => array(
                'label' => 'Read',
                'form' => array(
                    'type' => 'text',
                    'size' => '4',
                ),
            ),
            'save' => array(
                'label' => '',
                'form' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                ),
            ),
        );
    }
)
?>
