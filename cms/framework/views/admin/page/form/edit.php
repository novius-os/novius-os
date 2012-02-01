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
		$.nos.tabs.update($('#<?= $uniqid = uniqid('id_') ?>'), {
			label : '<?= $page->page_title ?>',
			iconUrl : 'static/cms/img/16/page.png'
		});
	});
});
</script>

<div id="<?= $uniqid ?>" class="page">
<?php
$fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");

foreach ($fieldset->field() as $field) {
	if ($field->type == 'checkbox') {
		$field->set_template('{field} {label}');
	}
}

$fieldset->field('page_cache_duration')->set_template('{label} {field} seconds');
$fieldset->field('page_lock')->set_template('{label} {field}');
?>

<?= $fieldset->open('admin/admin/page/form/edit/'.$page->page_id); ?>
<?= View::forge('form/layout_standard', array(
	'css_id' => $uniqid,

	'fieldset' => $fieldset,
	'medias' => array(),
	'title' => 'page_title',
	'id' => 'page_id',

	'published' => 'page_published',
	'save' => 'save',

	'subtitle' => array('page_type', 'page_template'),

	'content' => \View::forge('form/expander', array(
		'title'    => 'Content',
		// Wysiwyg are edge-to-edge with the border
		'nomargin' => true,
		'content'  => '
			<div id="external">
				<table>
					'.$fieldset->field('page_external_link')->build().'
					'.$fieldset->field('page_external_link_type')->build().'
				</table>
			</div>
			<div id="internal" style="display:none;">
				<p style="padding:1em;">We\'re sorry, internal links are not supported yet. We need a nice page selector before that.</p>
			</div>
			<div id="wysiwyg" style="display:none;"></div>',
	), false),

	'menu' => array(
		'Menu' => array('page_menu', 'page_menu_title'),
		'SEO' => array('page_virtual_name', 'page_meta_noindex', 'page_meta_title', 'page_meta_description', 'page_meta_keywords'),
		'Admin' => array('page_cache_duration', 'page_lock'),
	),
), false); ?>
<?= $fieldset->close(); ?>
</div>

<script type="text/javascript">
require([
	'static/cms/js/jquery/tinymce/jquery.tinymce_src',
	'static/cms/js/jquery/tinymce/jquery.wysiwyg',
	'jquery-nos'
], function(a,b,$) {
	$(function() {

		$('input[name=page_meta_noindex]').change(function() {
			$(this).closest('p').nextAll()[$(this).is(':checked') ? 'hide' : 'show']();
		}).change();


		$('input[name=page_menu]').change(function() {
			$(this).closest('p').nextAll()[$(this).is(':checked') ? 'show' : 'hide']();
		}).change();

		$('select[name=page_template]').bind('change', function() {
			$.ajax({
				url: 'admin/admin/page/ajax/wysiwyg/<?= $page->page_id ?>',
				data: {
					template_id: $(this).val()
				},
				dataType: 'json',
				success: function(data) {

					var ratio = $('#wysiwyg').width() * 3 / 5;
					$('#wysiwyg').empty().css({
						height: ratio,
						overflow: 'visible'
					});
					$.each(data.layout, function(i) {
						coords = this.split(',');
						var bloc = $('<div></div>').css({
							position: 'absolute',
							left:   Math.round(coords[0] / data.cols * 100) + '%',
							top:    Math.round(coords[1] / data.rows * ratio),
							width:  Math.round(coords[2] / data.cols * 100) + '%',
							height: Math.round(coords[3] / data.rows * ratio)
						}).append(
							$('<textarea></textarea>')
							.val(data.content[i])
							.attr({name: 'wysiwyg[' + i + ']'})
							.addClass('wysiwyg')
							.css({
								display: 'block',
								width: '100%',
								height: Math.round(coords[3] / data.rows * ratio),
								border: 0,
								boxShadow: 'inset 0px 0px 2px 2px  #888'
							}));
						$('#wysiwyg').append(bloc);
						// The bottom row from TinyMCE is roughly 21px
						$('#wysiwyg [name="wysiwyg[' + i + ']"]').wysiwyg({
							height: (coords[3] / data.rows * ratio) - 21
						});
					});
				}
			})
		});

		$('select[name=page_type]').change(function() {
			var val = $(this).val();

			if (val == <?= Cms\Model_Page_Page::TYPE_CLASSIC ?> || val == <?= Cms\Model_Page_Page::TYPE_FOLDER ?>) {
				$('#wysiwyg').show().siblings().hide();
				$('select[name=page_template]').closest('div.unit').show().end().change();
			}

			if (val == <?= Cms\Model_Page_Page::TYPE_EXTERNAL_LINK ?>) {
				$('#external').show().siblings().hide();
				$('select[name=page_template]').closest('div.unit').hide();
			}

			if (val == <?= Cms\Model_Page_Page::TYPE_INTERNAL_LINK ?>) {
				$('#internal').show().siblings().hide();
				$('select[name=page_template]').closest('div.unit').hide();
			}
		}).change();
	});
});</script>
