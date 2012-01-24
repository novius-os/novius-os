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
require(['jquery-nos'], function($) {
	$(function() {
		$(":input[type='text'],:input[type='password'],textarea").wijtextbox();
		$(":input[type='submit'],button").button();
		$("select").wijdropdown();
		$('.fieldset').wijexpander({expanded: true });
	});
});
require(['jquery-nos', 'static/cms/js/jquery/jquery-form/jquery.form.min'], function($) {
	$('form').submit(function() {
		var self = this;
		$(self).ajaxSubmit({
			dataType: 'json',
			success: function(json) {
				$.nos.listener.fire('wysiwyg.module.save', true, [json]);
			},
			error: function(error) {
				$.nos.notify('An error occured', 'error');
			}
		});
		return false;
	});
});
</script>

<div class="page myPage">
	<form method="POST" action="admin/cms_blog/preview">
	<div class="line myBody">
		<div class="unit col c1"></div>
		<div class="unit col c5 ui-widget">
		</div>
		<div class="unit col c5 ui-widget">
			<div class="fieldset">
				<h3>Options</h3>
				<div>
					<p><label for="item_per_page">Item per page:</label> <input type="text" name="item_per_page" id="item_per_page" /></p>
					<p><input type="checkbox" name="link_on_title" id="link_on_title" /> <label for="link_on_title">Link on title</label></p>
				</div>
			</div>
		</div>
		<div class="unit lastUnit"></div>
	</div>
	<div class="line">
		<div class="unit col c1"></div>
		<div class="unit col c10 ui-widget">
			<input type="submit" value="Save" /> or Cancel
		</div>
		<div class="unit lastUnit"></div>
	</div>
	</form>
</div>