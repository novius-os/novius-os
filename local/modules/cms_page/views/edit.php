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
		$("#accordion").wijaccordion({
			header: "h3"
		});
		$(":input[type='text'],:input[type='password'],textarea").wijtextbox();
		$(":input[type='submit'],button").button();
		$("select").wijdropdown();
		$('.fieldset').wijexpander({expanded: true });
	});
});
</script>

<div class="page myPage">
	<?= $fieldset->open('admin/cms_page/form/edit/'.$page->page_id); ?>
	<div class="line myBody">
		<div class="unit col c1"></div>
		<div class="unit col c7 ui-widget" style="position:relative;z-index:100;">
			<?= $fieldset->field('page_titre')
				->set_template('{field}')
				->set_attribute('class', 'title c4');
			?>
			<table>
			<?= $fieldset->field('page_id')->build(); ?>
			<?= $fieldset->field('page_gab_id')->build(); ?>
			</table>
			<div class="fieldset">
				<h3>Content</h3>
				
				<div id="wysiwyg"></div>
				
			</div>
			<p><?= $fieldset->field('save')->build(); ?> or <a href="#" onclick="javascript:$.nos.tabs.close();return false;">Cancel</a></p>
		</div>
		<?php
		$fieldset->form()->set_config('field_template',  "\t\t<span class=\"{error_class}\">{label}{required}</span>\n\t\t<br />\n\t\t<span class=\"{error_class}\">{field} {error_msg}</span>\n");
		?>
		<div class="unit col c3" style="position:relative;z-index:99;">
			 <div id="accordion">
				<div>
					<h3>
						<a href="#">SEO</a></h3>
					<div>
						<p><?= $fieldset->field('page_nom_virtuel')->build(); ?>.html</p>
						<p><?= $fieldset->field('page_titre_reference')->build(); ?></p>
						<p><?= $fieldset->field('page_description')->build(); ?></p>
						<p><?= $fieldset->field('page_keywords')->build(); ?></p>
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
		$('select[name=page_gab_id]').change(function() {
			$.ajax({
				url: 'admin/cms_page/ajax/wysiwyg/<?= $page->page_id ?>',
				data: {
					template_id: $(this).val()
				},
				dataType: 'json',
				success: function(data) {
					
					var ratio = $('#wysiwyg').width() * 3 / 4;
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
						// The bottom row from TinyMCE is roughly 20px
						$('#wysiwyg [name="wysiwyg[' + i + ']"]').wysiwyg({
							height: (coords[3] / data.rows * ratio) - 20
						});
					});
				}
			})
		}).change();
	});
});</script>
