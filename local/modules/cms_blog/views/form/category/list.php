<?php
$categories = Cms\Blog\Model_Category::findOrdered();
$obj_cats = $object->categories;
$obj_cats_ids = Arr::assoc_to_keyval($obj_cats, 'blgc_id', 'blgc_id');
?>
<?php
foreach ($categories as $category) {
    echo render('cms_blog::form/category/item', array('category' => $category, 'level' => 0, 'obj_cats_ids' => $obj_cats_ids));
}
?>