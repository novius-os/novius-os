<script type="text/javascript">
require(['jquery-nos', 'jquery-ui', 'jquery'], function($) {
	$(function() {

		$('body').height($(window).height());
		$('#tabs').height($(window).height());
		$('iframe').height($(window).height());

		var base_url = '<?= \Uri::base(true) ?>';

		var $height = $('#height');
		var $width  = $('#width');
		var $title  = $('#title');
		var $alt    = $('#alt');
		var $style  = $('#style');

		var $proportional   = $('#proportional');
		var $same_title_alt = $('#same_title_alt');

		var media = null;

		$.nos.listener.add('tinymce.image_select', true, function(media_json, image_dom) {
			media = media_json;

			console.log('inside callback');
			console.log(arguments);

			if (image_dom == null)
			{

				$height.val(media_json.height);
				$width.val(media_json.width);
				$title.val(media_json.title);
				$alt.val(media_json.title);
				$style.val('');

				$($('#tabs li a').get(1)).click();
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
		});

		$('#close').click(function() {
			$.nos.listener.fire('tinymce.image_close', true);
		});

		$('#save').click(function() {
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

			console.log('save clicked');
		});

		// Proportianal width & height
		$width.bind('change keyup', function() {
			if ($proportional.is(':checked') && media && media.width && media.height) {
				$height.val(Math.round($width.val() * media.height / media.width));
			}
		});
		$proportional.change(function() {
			if ($(this).is(':checked')) {
				$('#height').attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
				$width.triggerHandler('change');
			} else {
				$('#height').removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
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
				$('#alt').attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
			} else {
				$('#alt').removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
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

				console.log('get media data with ajax');
				$.ajax({
					method: 'GET',
					url: base_url + 'admin/cms_media/info/media/' + media_id,
					dataType: 'json',
					success: function(item) {
						$.nos.listener.fire('tinymce.image_select', true, [item, $img]);
					}
				})
			} else {
				console.log('use current data from media');
				$.nos.listener.fire('tinymce.image_select', true, [$img.data('media'), $img]);
			}
		}

		$(":input[type='text'],:input[type='password'],textarea").wijtextbox();
		$(":input[type='submit'],button").button();
		$("select").wijdropdown();
		$(":input[type=checkbox]").wijcheckbox();
		$('.expander').wijexpander({expanded: true });
		$('.accordion').wijaccordion({
			header: "h3"
		});

		require(['static/cms/js/jquery/wijmo/js/jquery.wijmo.wijtabs.js'], function() {
			$('#tabs').wijtabs({
				alignment: 'left'
			});
		});

		$('iframe').each(function() {
			$(this).attr('src', $(this).data('src'));
		});
	});
});
</script>

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

#tabs > ul {
	width : 15%;
}

#tabs > div {
	width : 83%;
	margin-right : 1%;
}
</style>

<div id="tabs">
	<ul>
		<li><a href="#library">Media library</a></li>
		<li><a href="#properties">Properties</a></li>
	</ul>
	<iframe id="library" data-src="admin/admin/media/mode/tinymce/index"></iframe>
	<div id="properties">
		<form action="#">
			<table class="fieldset">
				<tr>
					<th><label>Title: </label></th>
					<td><input type="text" name="title" id="title" size="30" /></td>
				</tr>
				<tr>
					<th><label>Description: </label></th>
					<td><input type="text" name="alt" id="alt" size="30" /> &nbsp; <input type="checkbox" id="same_title_alt" checked> <label for="same_title_alt">&nbsp;Same as title</label></td>
				</tr>
				<tr>
					<th><label>Width: </label></th>
					<td><input type="text" name="width" id="width" size="5" /> &nbsp; <input type="checkbox" id="proportional" checked> <label for="proportional">&nbsp;Keep proportions</label></td>
				</tr>
				<tr>
					<th><label>Height: </label></th>
					<td><input type="text" name="height" id="height" size="5" readonly /></td>
				</tr>
				<tr>
					<th><label>Style: </label></th>
					<td><input type="text" name="style" id="style" size="50" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<p style="position:absolute;width:190px;bottom:20px;left:10px;text-align:center;">
	<button id="save">Save</button> &nbsp; or &nbsp; <a id="close" href="#">Cancel</a>
</p>