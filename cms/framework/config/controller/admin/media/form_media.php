<?php

return array(
    'id' => array (
        'label' => 'ID: ',
        'widget' => 'text',
    ),
    'media_path_id' => array(
        'form' => array(
            'type'  => 'hidden',
        ),
        'label' => __('Choose a folder where to put your media:'),
    ),
    'media' => array(
        'form' => array(
            'type' => 'file',
        ),
        'label' => __('File from your hard drive: '),
    ),
    'media_title' => array(
        'form' => array(
            'type' => 'text',
        ),
        'label' => __('Title: '),
    ),
    'slug' => array(
        'form' => array(
            'type' => 'text',
        ),
        'label' => __('SEO, Media URL: '),
    ),
    'save' => array(
        'form' => array(
            'type' => 'submit',
            'tag'  => 'button',
            'class' => 'primary',
            'value' => __('Save'),
            'data-icon' => 'check',
        ),
    ),
);