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
<form method="POST" action="admin/admin/media/folder/do">
	<input type="hidden" name="medif_parent_id" value="<?= $folder->medif_id ?>" />
	<p><label>Title: <input type="text" name="medif_title" /></label></p>
	<p><label>Path: <?= $folder->medif_path ?><input type="text" name="medif_path" /></label></p>
	<p><input type="submit" value="Create the sub-diretory" ></p>
</form>
<script type="text/javascript">
require(['jquery-nos', 'static/cms/js/jquery/jquery-form/jquery.form.min'], function($) {
	$('form').submit(function(e) {
		$(this).ajaxSubmit({
			dataType: 'json',
			success: function(json) {
				console.log(json);
				if (json.error) {
					$.nos.notify(json.error, 'error');
				}
				if (json.notify) {
					$.nos.notify(json.notify);
				}
				if (json.listener_fire) {
					$.nos.listener.fire(json.listener_fire, json.listener_bubble || true, json.listener_data);
				}
				if (json.redirect) {
					document.location = json.redirect;
				}

				// Close at the end!
				if (json.closeTab) {
					$.nos.tabs.close();
				}
				if (json.closeDialog) {
					window.parent.jQuery(':wijmo-wijdialog')
						.wijdialog('close')
						.wijdialog('destroy')
						.remove();
				}
			},
			error: function() {
				$.nos.notify('An error occured', 'error');
			}
		});
		e.preventDefault();
	});
});
</script>	