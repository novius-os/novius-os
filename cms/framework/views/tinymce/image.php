<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

	$mp3view = (string) Request::forge('cms/admin/media/list/index')->execute(array('image_pick'))->response();
	$uniqid = uniqid('tabs_');
	$id_library = $uniqid.'_library';
	$id_properties = $uniqid.'_properties';
?>
<style type="text/css">
	.box-sizing-border {
		box-sizing: border-box;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		height: 100%;
	}
</style>
<div id="<?= $uniqid ?>" class="box-sizing-border">
	<ul>
		<li><a href="#<?= $id_library ?>"><?= $edit ? __('Pick a new image') : __('1. Pick your image') ?></a></li>
		<li><a href="#<?= $id_properties ?>"><?= $edit ? __('Edit properties') : __('2. Set the properties') ?></a></li>
	</ul>
	<div id="<?= $id_library ?>" class="box-sizing-border"></div>

	<form action="#">
		<div id="<?= $id_properties ?>">
			<table class="fieldset">
				<tr>
					<td rowspan="6"><img /></td>
					<th><label><?= __('Title:') ?> </label></th>
					<td><input type="text" name="title" data-id="title" size="30" /></td>
				</tr>
				<tr>
					<th><label><?= __('Description:') ?> </label></th>
					<td><input type="text" name="alt" data-id="alt" size="30" /> &nbsp; <label><input type="checkbox" data-id="same_title_alt" checked> &nbsp;<?= strtr(__('Use {field}'), array('{field}' => __('title'))) ?></label></td>
				</tr>
				<tr>
					<th><label><?= __('Width:') ?> </label></th>
					<td><input type="text" name="width" data-id="width" size="5" /> &nbsp; <label><input type="checkbox" data-id="proportional" checked> &nbsp;<?= __('Keep proportions') ?></label></td>
				</tr>
				<tr>
					<th><label><?= __('Height:') ?> </label></th>
					<td><input type="text" name="height" data-id="height" readonly /></td>
				</tr>
				<tr>
					<th><label><?= __('Style:') ?> </label></th>
					<td><input type="text" name="style" data-id="style" size="50" /></td>
				</tr>
				<tr>
					<th></th>
					<td> <button type="submit" class="primary" data-icon="check" data-id="save"><?= __('Insert this image') ?></button> &nbsp; <?= __('or') ?> &nbsp; <a data-id="close" href="#"><?= __('Cancel') ?></a></td>
				</tr>
			</table>
		</div>
	</form>
</div>
<script type="text/javascript">
require(['jquery-nos'], function($) {
	$(function() {

		var id = '<?= $uniqid ?>',
			newimg = !'<?= $edit ?>',
			$container = $('#' + id)
				.find('> form')
				.submit(function(e) {
					$container.find('input[data-id=save]').triggerHandler('click');
					e.stopPropagation();
					e.preventDefault();
				})
				.end()
				.find('a[data-id=close]')
				.click(function(e) {
					$dialog.wijdialog('close');
					e.preventDefault();
				})
				.end()
				.find('input[data-id=save]')
				.click(function(e) {
					var img = $('<img />');

					if (!media || !media.id) {
						alert(<?= \Format::forge()->to_json(__('Please choose an image first')) ?>);
						return;
					}

					img.attr('height', $height.val());
					img.attr('width',  $width.val());
					img.attr('title',  $title.val());
					img.attr('alt',    $alt.val());
					img.attr('style',  $style.val());

					img.attr('data-media', JSON.stringify(media));
					img.attr('src', base_url + media.path);

					$dialog.trigger('insert.media', img);
					e.stopPropagation();
					e.preventDefault();
				})
				.end()
				.find('> ul')
				.css({
					width : '18%'
				})
				.end(),
			$dialog = $container.closest('.ui-dialog-content')
				.bind('select.media', function(e, data) {
					tinymce_image_select(data);
				}),
			$library = $container.find('div:eq(0)')
				.css({
					width : '100%',
					padding: 0,
					margin: 0
				}),
			$thumb = $container.find('img')
				.hide()
				.parent()
				.css('vertical-align', 'top')
				.end(),
			base_url = '<?= \Uri::base(true) ?>',
			$height = $container.find('input[data-id=height]'),
			$width = $container.find('input[data-id=width]')
				.bind('change keyup', function() {
					if ($proportional.is(':checked') && media && media.width && media.height) {
						$height.val(Math.round($width.val() * media.height / media.width));
					}
				}),
			$title = $container.find('input[data-id=title]')
				.bind('change keyup', function() {
					if ($same_title_alt.is(':checked')) {
						$alt.val($title.val());
					}
				}),
			$alt = $container.find('input[data-id=alt]'),
			$style = $container.find('input[data-id=style]'),
			$proportional = $container.find('input[data-id=proportional]')
				.change(function() {
					if ($proportional.is(':checked')) {
						$height.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
						$width.triggerHandler('change');
					} else {
						$height.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
					}
				}),
			$same_title_alt = $container.find('input[data-id=same_title_alt]')
				.change(function() {
					if ($same_title_alt.is(':checked')) {
						$alt.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
					} else {
						$alt.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
					}
				}),
			media = null,
			tinymce_image_select = function(media_json, image_dom) {
					media = media_json;

					$thumb.attr('src', media.thumbnail.replace(/64/g, '128'))
						.show();

					if (image_dom == null)
					{
						$height.val(media_json.height);
						$width.val(media_json.width);
						$title.val(media_json.title);
						$alt.val(media_json.title);
						$style.val('');

						$container.wijtabs('enableTab', 1)
							.wijtabs('select', 1);
						return;
					}

					$height.val(image_dom.attr('height'));
					$width.val(image_dom.attr('width'));
					$title.val(image_dom.attr('title'));
					$alt.val(image_dom.attr('alt'));
					$style.val(image_dom.attr('style'));

					if (media && (Math.round($width.val() * media.height / media.width) != $height.val())) {
						$proportional.prop('checked', false).removeAttr('checked', true).change();
					}

					if ($title.val() != $alt.val())
					{
						$same_title_alt.prop('checked', false).removeAttr('checked').change();
					}
				},
			ed = $dialog.data('tinymce'),
			e = ed.selection.getNode();

		$proportional.triggerHandler('change');
		$same_title_alt.triggerHandler('change');

		// Editing the current image
		if (e.nodeName == 'IMG') {
			var $img = $(e),
				media_id = $img.data('media-id');

			// No data available yet, we need to fetch them
			if (media_id) {
				$.ajax({
					method: 'GET',
					url: base_url + 'admin/media/info/media/' + media_id,
					dataType: 'json',
					success: function(item) {
						tinymce_image_select(item, $img);
					}
				})
			} else {
				tinymce_image_select($img.data('media'), $img);
			}
		}

		$container.wijtabs({
				alignment: 'left',
				load: function(e, ui) {
					var margin = $(ui.panel).outerHeight(true) - $(ui.panel).innerHeight();
					$(ui.panel).height($dialog.height() - margin);
				},
				disabledIndexes: newimg ? [1] : []
			})
			.find('.wijmo-wijtabs-content')
			.css('width', '81%')
			.addClass('box-sizing-border');
		$.nos.ui.form($container);


		if (!newimg) {
			$container.wijtabs('select', 1)
				.bind('wijtabsshow', function() {
					$library.html(<?= \Format::forge()->to_json($mp3view) ?>);
				});
		} else {
			$library.html(<?= \Format::forge()->to_json($mp3view) ?>);
		}
	});
});
</script>