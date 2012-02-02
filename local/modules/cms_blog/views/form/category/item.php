<div style="padding-left: <?= $level * 20 ?>px;">
    <input type="checkbox" name="categories[]" value="<?= $category->blgc_id ?>" <?= $obj_cats_ids[$category->blgc_id] ? 'checked' : '' ?> /><?= $category->blgc_title ?>
</div>
<?php
foreach ($category->childrens as $category) {
    echo render('cms_blog::form/category/item', array('category' => $category, 'level' => $level + 1));
}
?>