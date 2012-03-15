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
			label : <?= json_encode($page->page_title) ?>,
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

$checkbox = '<br /><label><input type="checkbox" data-id="same_title">'.strtr(__('Use {field}'), array('{field}' => __('title'))).'</label>';
$fieldset->field('page_menu_title')->set_template("\t\t<span class=\"{error_class}\">{label}{required}</span>\n\t\t<br />\n\t\t<span class=\"{error_class}\">{field} $checkbox {error_msg}</span>\n");

/*
$short_url = $page->page_id; // Needs an application page
$short_link = \View::forge('form/short_link', array(
    'short_url' => $short_url,
));
$qrcode = \View::forge('form/qrcode', array(
    'url' => $short_url,
));
*/
?>

<?= $fieldset->open('admin/admin/page/form/edit/'.$page->page_id); ?>
<?= View::forge('form/layout_standard', array(
	'css_id' => $uniqid,

	'fieldset' => $fieldset,
    // Used by the behaviours (publishable, etc.)
    'object' => $page,
	'medias' => array(),
	'title' => 'page_title',
	'id' => 'page_id',

	'save' => 'save',

	'subtitle' => array('page_type', 'page_template'),

	'content' => \View::forge('form/expander', array(
		'title'    => 'Content',
		// Wysiwyg are edge-to-edge with the border
		'nomargin' => true,
        'options' => array(
            'allowExpand' => false,
        ),
		'content'  => '
			<div id="'.($uniqid_external = uniqid('external_')).'">
				<table>
					'.$fieldset->field('page_external_link')->build().'
					'.$fieldset->field('page_external_link_type')->build().'
				</table>
			</div>
			<div id="'.($uniqid_internal = uniqid('internal_')).'" style="display:none;">
				<p style="padding:1em;">We\'re sorry, internal links are not supported yet. We need a nice page selector before that.</p>
			</div>
			<div id="'.($uniqid_wysiwyg = uniqid('wysiwyg_')).'" style="display:none;"></div>',
	), false),

	'menu' => array(
		__('Menu') => array('page_menu', 'page_menu_title'),
        __('URL (page address)') => array('page_virtual_name'),
		__('SEO') => array('page_meta_noindex', 'page_meta_title', 'page_meta_description', 'page_meta_keywords'),
		__('Admin') => array('page_cache_duration', 'page_lock'),
	),
), false); ?>
<?= $fieldset->close(); ?>
</div>


<script type="text/javascript">
require([
	'static/cms/js/vendor/tinymce/jquery.tinymce_src',
	'static/cms/js/vendor/tinymce/jquery.wysiwyg',
	'jquery-nos'
], function(a,b,$) {
	$(function() {

        var $container = $('#<?= $uniqid ?>');

		$container.find('input[name=page_meta_noindex]').change(function() {
			$(this).closest('p').nextAll()[$(this).is(':checked') ? 'hide' : 'show']();
		}).change();


		$container.find('input[name=page_menu]').change(function() {
			$(this).closest('p').nextAll()[$(this).is(':checked') ? 'show' : 'hide']();
		}).change();

		$container.find('select[name=page_template]').bind('change', function() {
			$.ajax({
				url: 'admin/admin/page/ajax/wysiwyg/<?= $page->page_id ?>',
				data: {
					template_id: $(this).val()
				},
				dataType: 'json',
				success: function(data) {

                    var $wysiwyg = $container.find('#<?= $uniqid_wysiwyg ?>');
					var ratio = $wysiwyg.width() * 3 / 5;
					$wysiwyg.empty().css({
						height: ratio,
						overflow: 'visible'
					});
					$.each(data.layout, function(i) {
						var coords = this.split(',');
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
						$wysiwyg.append(bloc);
						// The bottom row from TinyMCE is roughly 21px
						$wysiwyg.find('[name="wysiwyg[' + i + ']"]').wysiwyg({
							height: (coords[3] / data.rows * ratio) - 21
						});
					});
				}
			})
		});

        var $template_unit = $('select[name=page_template]').closest('div.unit');
		$container.find('select[name=page_type]').change(function() {
			var val = $(this).val();

			if (val == <?= Cms\Model_Page_Page::TYPE_CLASSIC ?> || val == <?= Cms\Model_Page_Page::TYPE_FOLDER ?>) {
				$container.find('#<?= $uniqid_wysiwyg ?>').show().siblings().hide();
				$template_unit.show().end().change();
			}

			if (val == <?= Cms\Model_Page_Page::TYPE_EXTERNAL_LINK ?>) {
				$container.find('#<?= $uniqid_external ?>').show().siblings().hide();
				$template_unit.hide();
			}

			if (val == <?= Cms\Model_Page_Page::TYPE_INTERNAL_LINK ?>) {
				$container.find('#<?= $uniqid_internal ?>').show().siblings().hide();
				$template_unit.hide();
			}
		}).change();

        var $title      = $container.find('input[name=page_title]');
		var $menu_title = $container.find('input[name=page_menu_title]');
        var $checkbox   = $container.find('input[data-id=same_title]');
        $title.bind('change keyup', function() {
            if ($checkbox.is(':checked')) {
                $menu_title.val($title.val());
            }
        });
        if ($title.val() == $menu_title.val() || $menu_title.val() == '') {
            $checkbox.attr('checked', true).wijcheckbox("refresh");
        }
		$checkbox.change(function() {
			if ($(this).is(':checked')) {
				$menu_title.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
                $title.triggerHandler('change');
			} else {
				$menu_title.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
			}
		}).triggerHandler('change');
	});
});</script>