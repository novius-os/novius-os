<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

if (!empty($display['title'])) {
    $title = $item->blog_title;
    if (!empty($link_title)) {
        $title = '<a href="'.$link_on_title.'">'.$title.'</a>';
    }
    echo '<'.$title_tag.' class="billet_titre">'.$title.'</'.$title_tag.'>';
}

if (!empty($display['author'])) {
    $author = $item->author->fullname() ?: $item->blog_author;
    if (!empty($link_to_author)) {
        $author = '<a href="'.$link_to_author.'">'.$author.'</a>';
    }
    echo 'Posté par : '.$author;
}

if (!empty($display['date']) && !empty($created_at)) {
    $date = Date::forge($created_at)->format(isset($date_format) ? $date_format : 'eu_full');
    if (!empty($link_to_date)) {
        $date = '<a href="'.$link_to_date.'>'.$date.'</a>';
    }
    $styles = !empty($color) ? 'style="color:'.$color.';"' : '';
    echo '<span class="date" '.$styles.'>'.$date.'</span>';
}

if (!empty($display['summary']) && !empty($item->blog_summary)) {
    echo nl2br($item->blog_summary);
}

if (!empty($display['thumbnail']) && !empty($item->medias->thumbnail)) {
    echo $item->medias->thumbnail->get_img_tag_resized(200);
}

if (!empty($display['wysiwyg']) && !empty($item->wysiwygs)) {
    echo $item->wysiwygs->content;
}

if (!empty($display['tags'])) {
    echo '<span style="padding-right:5px;" class="tags_titre">Tags :</span>';

    $tags = array();
    foreach ($item->tags as $tag) {
        $tags[$link_to_tag($tag->tag_label)] = $tag->tag_label;
    }
    echo implode(', ', array_map(function($href, $title) {
        return '<a href="'.$href.'">'.$title.'</a>';
    }, array_keys($tags), array_values($tags)));
}


if (!empty($display['categories']) && !empty($item->categories)) {
    echo '<span class="categories_titre" style="padding-right:5px;">Catégorie :</span>';
    $categories = array();
    foreach ($item->categories as $category) {
        $categories[$link_to_category($category)] = $category->blgc_title;
    }
    echo implode(', ', array_map(function($href, $title) {
        return '<a href="'.$href.'">'.$title.'</a>';
    }, array_keys($categories), array_values($categories)));
}

if (!empty($display['stats'])) {
    if (empty($comments_count)) {
        echo '<a href="'.$link_to_item.'#commentaires">Aucun commentaire</a>';
    } else {
        echo '<a href="'.$link_to_item.'#commentaires">'.$comments_count.' commentaire'.($comments_count > 1 ? 's' : '').'</a>';
    }
}