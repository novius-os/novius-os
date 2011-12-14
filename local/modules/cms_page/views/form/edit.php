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
		$.nos.tabs.updateTab({
			label : '<?= $page->page_titre ?>',
			iconUrl : 'static/modules/cms_page/img/16/page.png'
		});
	});
});
</script>

<script type="text/javascript">
require(['jquery-nos'], function($) {
	$(function() {
		$(":input[type='text'],:input[type='password'],textarea").wijtextbox();
		$(":input[type='submit'],button").button();
		$("select").wijdropdown();
		$(":input[type=checkbox]").wijcheckbox();
		$('.expander').wijexpander({expanded: true });
		$("#accordion").wijaccordion({
			header: "h3"
		});
	});
});
</script>

<style type="text/css">
.wijmo-checkbox {
	display: inline-block;
	width: inherit;
}
.wijmo-checkbox label {
	width: inherit;
}
.ui-helper-clearfix:after {
	content: '';
}
.mceExternalToolbar {
	z-index:100;
}
</style>

<div class="page myPage myBody">
	<?= $fieldset->open('admin/cms_page/form/edit/'.$page->page_id); ?>
	<div class="line ui-widget">
		<div class="unit col c1"></div>
		<div class="unit col c7" id="line_first" style="position:relative;z-index:99;">
			<div class="line" style="margin-bottom:1em;">
				<?= $fieldset->field('page_titre')
					->set_template('{field}')
					->set_attribute('class', 'title c4');
				?>
				<?= $fieldset->field('page_id')->set_template('{label} {field}')->build(); ?>

				<?php
				$fieldset->form()->set_config('field_template',  "<p class=\"{error_class}\">{label}{required} {field} {error_msg}</p>");
				?>
			</div>
			<div class="line" style="margin-bottom:1em;overflow:visible;">
				<div class="unit col"><?= $fieldset->field('page_type')->build(); ?></div>
				<div class="unit col"><?= $fieldset->field('page_gab_id')->build(); ?></div>
			</div>
		</div>
		<div class="unit col c3" style="position:relative;z-index:98;text-align:center;">
			<p style="margin: 0 0 1em;"><?= $fieldset->field('page_publier')->set_template('{field} {label}')->build(); ?></p>
			<p><?= $fieldset->field('save')->set_template('{field}')->build(); ?> &nbsp; or &nbsp; <a href="#" onclick="javascript:$.nos.tabs.close();return false;">Cancel</a></p>
		</div>
	</div>
	<?php
	$fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");
	?>
	<div class="line  ui-widget">
		<div class="unit col c1"></div>
		<div class="unit col c7" id="line_second" style="position:relative;margin-bottom:1em;">
			<div class="expander fieldset">
				<h3>Content</h3>
				<div style="overflow:visible">
					<div id="external">
						<table>
							<?= $fieldset->field('page_lien_externe')->build(); ?>
							<?= $fieldset->field('page_lien_externe_type')->build(); ?>
						</table>
					</div>
					<div id="internal" style="display:none;">
						<p style="padding:1em;">We're sorry, internal links are not supported yet. We need a nice page selector before that.</p>
					</div>
					<div id="wysiwyg" style="display:none;"></div>
				</div>

			</div>
		</div>
		<?php
		$fieldset->form()->set_config('field_template',  "\t\t<span class=\"{error_class}\">{label}{required}</span>\n\t\t<br />\n\t\t<span class=\"{error_class}\">{field} {error_msg}</span>\n");
		?>
		<div class="unit col c3" style="position:relative;z-index:98;margin-bottom:1em;">
			 <div id="accordion">
				<div>
					<h3>
						<a href="#">Menu</a></h3>
					<div>
						<p><?= $fieldset->field('page_menu')->set_template('{field} {label}')->build(); ?></p>
						<p><?= $fieldset->field('page_titre_menu')->build(); ?></p>
					</div>
				</div>
				<div>
					<h3>
						<a href="#">SEO</a></h3>
					<div>
						<p><?= $fieldset->field('page_nom_virtuel')->build(); ?>.html</p>
						<p><?= $fieldset->field('page_noindex')->set_template('{field} {label}')->build(); ?></p>
						<p><?= $fieldset->field('page_titre_reference')->build(); ?></p>
						<p><?= $fieldset->field('page_description')->build(); ?></p>
						<p><?= $fieldset->field('page_keywords')->build(); ?></p>
					</div>
				</div>
				<div>
					<h3>
						<a href="#">Admin</a></h3>
					<div style="overflow:visible;">
						<p><?= $fieldset->field('page_duree_vie')->set_template('{label} {field} seconds')->build(); ?></p>
						<p><?= $fieldset->field('page_verrou')->set_template('{label} {field}')->build(); ?></p>
					</div>
				</div>
			 </div>
		 </div>
		<div class="unit lastUnit"></div>
	</div>
	<?= $fieldset->close(); ?>
</div>

<script type="text/javascript">
require([
	'static/cms/js/jquery/tinymce/jquery.tinymce_src',
	'static/cms/js/jquery/tinymce/jquery.wysiwyg',
	'jquery-nos'
], function(a,b,$) {
	$(function() {

		$('input[name=page_noindex]').change(function() {
			$(this).closest('p').nextAll()[$(this).is(':checked') ? 'hide' : 'show']();
		}).change();


		$('input[name=page_menu]').change(function() {
			$(this).closest('p').nextAll()[$(this).is(':checked') ? 'show' : 'hide']();
		}).change();

		$('select[name=page_gab_id]').bind('change', function() {
			console.log('change happened');
			$.ajax({
				url: 'admin/cms_page/ajax/wysiwyg/<?= $page->page_id ?>',
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
							left:   (coords[0] / data.cols * 100) + '%',
							top:    (coords[1] / data.rows * ratio),
							width:  (coords[2] / data.cols * 100) + '%',
							height: (coords[3] / data.rows * ratio)
						}).append(
							$('<textarea></textarea>')
							.val(data.content[i])
							.attr({name: 'wysiwyg[' + i + ']'})
							.addClass('wysiwyg')
							.css({
								display: 'block',
								width: '100%',
								height: (coords[3] / data.rows * ratio),
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

			if (val == <?= Cms\Page\Model_Page::TYPE_CLASSIC ?> || val == <?= Cms\Page\Model_Page::TYPE_FOLDER ?>) {
				$('#wysiwyg').show().siblings().hide();
				$('select[name=page_gab_id]').closest('div.unit').show().end().change();
			}

			if (val == <?= Cms\Page\Model_Page::TYPE_EXTERNAL_LINK ?>) {
				$('#external').show().siblings().hide();
				$('select[name=page_gab_id]').closest('div.unit').hide();
			}

			if (val == <?= Cms\Page\Model_Page::TYPE_INTERNAL_LINK ?>) {
				$('#internal').show().siblings().hide();
				$('select[name=page_gab_id]').closest('div.unit').hide();
			}
		}).change();
	});
});</script>
