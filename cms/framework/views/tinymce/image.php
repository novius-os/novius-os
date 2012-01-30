<div id="<?= $uniqid = uniqid('tabs_') ?>">
	<ul class="tabs">
		<li><a href="#<?= $id_library = $uniqid.'_library' ?>">Media library</a></li>
		<li><a href="#<?= $id_properties = $uniqid.'_properties' ?>">Properties</a></li>
	</ul>
	<div id="<?= $id_library ?>">
		<?php
		// We could load this using ajax, but it's faster to preload it directly here
		echo Request::forge('admin/media/index?view=tinymce')->execute()->response();
		?>
	</div>
	<div id="<?= $id_properties ?>">
		<form action="#" id="<?= $uniqid_form = uniqid('form_') ?>">
			<table class="fieldset">
				<tr>
					<th><label>Title: </label></th>
					<td><input type="text" name="title" data-id="title" size="30" /></td>
				</tr>
				<tr>
					<th><label>Description: </label></th>
					<td><input type="text" name="alt" data-id="alt" size="30" /> &nbsp; <input type="checkbox" data-id="same_title_alt" checked> <label for="same_title_alt">&nbsp;Same as title</label></td>
				</tr>
				<tr>
					<th><label>Width: </label></th>
					<td><input type="text" name="width" data-id="width" size="5" /> &nbsp; <input type="checkbox" data-id="proportional" checked> <label for="proportional">&nbsp;Keep proportions</label></td>
				</tr>
				<tr>
					<th><label>Height: </label></th>
					<td><input type="text" name="height" data-id="height" readonly /></td>
				</tr>
				<tr>
					<th><label>Style: </label></th>
					<td><input type="text" name="style" data-id="style" size="50" /></td>
				</tr>
			</table>
		</form>
	</div>

	<p style="position:absolute;width:190px;bottom:20px;left:10px;text-align:center;">
		<button data-id="save">Save</button> &nbsp; or &nbsp; <a data-id="close" href="#">Cancel</a>
	</p>
</div>

<style type="text/css">
#library {
	width: 100%;
	padding:0;
}
.wijmo-checkbox {
	display: inline-block;
	width: inherit;
	vertical-align: middle;
}
.wijmo-checkbox label {
	width: inherit;
}

<?= '#'.$uniqid ?> > ul {
	width : 15%;
}

<?= '#'.$uniqid ?> > div {
	width : 83%;
	margin-right : 1%;
}
</style>

<script type="text/javascript">
require(['jquery-nos', 'jquery-ui', 'jquery'], function($) {
	$(function() {

		var $container = $('#<?= $uniqid ?>');

		var getMargin = function(el) {
			return el.outerHeight(true) - el.height();
		};

		require(['static/cms/js/vendor/wijmo/js/jquery.wijmo.wijtabs.js'], function() {
			setTimeout(function() {
				$container.wijtabs({
					alignment: 'left',
					load: function(e, ui) {
						var margin = $(ui.panel).outerHeight(true) - $(ui.panel).innerHeight();
						$(ui.panel).height($('#<?= $uniqid ?>').parent().height() - margin);
					}
				});

				var $dialog_content = $container.find('.ui-dialog-content');
				var $tabs = $container.find('.tabs');

				var $properties = $('#<?= $id_properties ?>');
				var $library    = $('#<?= $id_library ?>');

				var margin = 0;

				margin += getMargin($dialog_content);
				margin += getMargin($tabs);

				var height = $container.parent().height() - margin;

				$tabs.height(height);
				$properties.height(height - getMargin($properties));
				$library.css({padding:0, margin:0}).height(height);

				// Now tabs are created and the appropriate dimensions are set, load the mp3grid
				var mp3grid_tmp = $library.children().height(height - getMargin($library.children())).attr('id');
				$.nos.listener.fire('mp3grid.' + mp3grid_tmp, true, []);

				$.nos.ui.form('#<?= $uniqid ?>');
			}, 0);
		});

		var base_url = '<?= \Uri::base(true) ?>';

		var $height = $container.find('input[data-id=height]');
		var $width  = $container.find('input[data-id=width]');
		var $title  = $container.find('input[data-id=title]');
		var $alt    = $container.find('input[data-id=alt]');
		var $style  = $container.find('input[data-id=style]');

		var $proportional   = $container.find('input[data-id=proportional]');
		var $same_title_alt = $container.find('input[data-id=same_title_alt]');

		var media = null;

		var tinymce_image_select = function(media_json, image_dom) {
			media = media_json;

			if (image_dom == null)
			{
				$height.val(media_json.height);
				$width.val(media_json.width);
				$title.val(media_json.title);
				$alt.val(media_json.title);
				$style.val('');

				$($('#<?= $uniqid ?> li a').get(1)).click();
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
		}

		// This is called by the "Pick" action from the grid
		$.nos.listener.add('tinymce.image_select', true, tinymce_image_select);

		// This is called for cleanup in the _nosImage command from tinyMce when the popup closes
		$.nos.listener.add('tinymce.image_select.close_dialog', true, function() {
			$.nos.listener.remove('tinymce.image_select', true, tinymce_image_select);
			$.nos.listener.remove('tinymce.image_dialog_close', true, arguments.callee);
		});

		$container.find('a[data-id=close]').click(function(e) {
			$.nos.listener.fire('tinymce.image_close', true);
			e.preventDefault();
		});

		$container.find('button[data-id=save]').click(function() {
			var img = $('<img />');

			if (!media || !media.id) {
				alert('Please choose an image first');
				return;
			}

			img.attr('height', $height.val());
			img.attr('width',  $width.val());
			img.attr('title',  $title.val());
			img.attr('alt',    $alt.val());
			img.attr('style',  $style.val());

			img.attr('data-media', JSON.stringify(media));
			img.attr('src', base_url + media.path);

			$.nos.listener.fire('tinymce.image_save', true, [img]);
		});

		$('#<?= $uniqid_form ?>').submit(function(e) {
			$container.find('button[data-id=save]').triggerHandler('click');
			e.stopPropagation();
		});

		// Proportianal width & height
		$width.bind('change keyup', function() {
			if ($proportional.is(':checked') && media && media.width && media.height) {
				$height.val(Math.round($width.val() * media.height / media.width));
			}
		});
		$proportional.change(function() {
			if ($(this).is(':checked')) {
				$height.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
				$width.triggerHandler('change');
			} else {
				$height.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
			}
		}).triggerHandler('change');

		// Same title and description (alt)
		$title.bind('change keyup', function() {
			if ($same_title_alt.is(':checked')) {
				$alt.val($title.val());
			}
		});
		$same_title_alt.change(function() {
			if ($(this).is(':checked')) {
				$alt.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
			} else {
				$alt.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
			}
		}).triggerHandler('change');


		var tinymce = $.nos.data('tinymce');
		var ed = tinymce.editor;
		var e = ed.selection.getNode();

		// Editing the current image
		if (e.nodeName == 'IMG')
		{
			var $img = $(e);
			var media_id = $img.data('media-id');

			// No data available yet, we need to fetch them
			if (media_id) {

				//log('get media data with ajax');
				$.ajax({
					method: 'GET',
					url: base_url + 'admin/media/info/media/' + media_id,
					dataType: 'json',
					success: function(item) {
						tinymce_image_select(item, $img);
					}
				})
			} else {
				//log('use current data from media');
				tinymce_image_select($img.data('media'), $img);
			}
		}
	});
});
</script>