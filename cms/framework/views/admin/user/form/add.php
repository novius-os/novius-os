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
	$.nos.ui.form('#<?= $uniqid = uniqid('id_') ?>');
	$(function () {
		$.nos.tabs.update($('#<?= $uniqid ?>'), {
			label : 'Add a new user',
			iconUrl : 'static/cms/img/16/user.png'
		});
	});

});
</script>

<div id ="<?= $uniqid ?>" class="page">
	<div class="line myBody">
		<div class="unit col c1"></div>
		<div class="unit col c7 ui-widget">
			<?= $fieldset_add->open('admin/admin/user/form/add/'); ?>
			<div class="expander">
				<h3>Add a new user</h3>
				<div>
				<table>
					<?php
					foreach ($fieldset_add->field() as $f) {
						echo $f->build();
					}
					?>
				</table>
				</div>
			</div>
			<?= $fieldset_add->close(); ?>
		</div>
	</div>
</div>

