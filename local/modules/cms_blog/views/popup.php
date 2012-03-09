<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
	$id = uniqid('temp_');
?>
<div id="<?= $id ?>">
	<form method="POST" action="admin/cms_blog/preview">
		<div class="line myBody">
			<div class="unit col c1"></div>
			<div class="unit col c10 ui-widget">
				<div class="expander">
					<h3>Options</h3>
					<div>
						<p><label for="item_per_page">Item per page:</label> <input type="text" name="item_per_page" id="item_per_page" value="<?= \Fuel\Core\Input::post('item_per_page', 10) ?>" /></p>
						<p><input type="checkbox" name="link_on_title" id="link_on_title" value="1" <?= \Fuel\Core\Input::post('link_on_title', 0) ? 'checked' : '' ?> /> <label for="link_on_title">Link on title</label></p>
					</div>
				</div>
			</div>
			<div class="unit lastUnit"></div>
		</div>
		<div class="line">
			<div class="unit col c1"></div>
			<div class="unit col c10 ui-widget">
				<button type="submit" data-icon="check">Save</button> or <a data-id="close" href="#">Cancel</a>
			</div>
			<div class="unit lastUnit"></div>
		</div>
	</form>
</div>

<script type="text/javascript">
require([
	'jquery-nos',
	'static/cms/js/vendor/jquery/jquery-form/jquery.form.min'
	], function($) {
		$(function() {
			var div = $('#<?= $id ?>')
				.find('a[data-id=close]')
				.click(function(e) {
					div.closest('.ui-dialog-content').wijdialog('close');
					e.preventDefault();
				})
				.end()
				.find('form')
				.submit(function() {
					var self = this;
					$(self).ajaxSubmit({
						dataType: 'json',
						success: function(json) {
							div.closest('.ui-dialog-content').trigger('save.enhancer', json);
						},
						error: function(error) {
							$.nos.notify('An error occured', 'error');
						}
					});
					return false;
				})
				.end();

			$.nos.ui.form(div);
		});
	});
</script>

