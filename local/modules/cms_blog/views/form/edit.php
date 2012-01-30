<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

?>
<script type="text/javascript">
    require(['jquery-nos'], function ($) {
        $(function () {
            $.nos.tabs.update({
                label : <?= \Format::forge()->to_json($object->blog_titre) ?>,
                iconUrl : 'static/modules/cms_blog/img/16/blog.png'
            });
        });
    });
</script>


<style type="text/css">
/* ? */
/* @todo check this */
.ui-accordion-content-active {
	overflow: visible !important;
}
</style>

<div class="page myPage myBody">
<?php
$fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");

foreach ($fieldset->field() as $field) {
	if ($field->type == 'checkbox') {
		$field->set_template('{field} {label}');
	}
}

$fieldset->field('blog_lu')->set_template('{label} {field} times');
?>

<?= $fieldset->open('admin/cms_blog/form/edit/'.$object->blog_id); ?>
<?= View::forge('form/layout_standard', array(
	'fieldset' => $fieldset,
	'medias' => array('media->thumbnail->medil_media_id'),
	'title' => 'blog_titre',
	'id' => 'blog_id',

	'published' => 'blog_date_debut_publication',
	'save' => 'save',

	'subtitle' => array(),

	'content' => \View::forge('form/expander', array(
		'title'   => 'Content',
		'content' => $fieldset->field('wysiwyg->content->wysiwyg_text'),
	), false),

	'menu' => array(
		'Meta' => array('author->user_fullname', 'blog_auteur', 'blog_date_creation', 'blog_lu'),
		'Categories' => array(),
		'Tags' => array(),
	),
), false); ?>
<?= $fieldset->close(); ?>
</div>