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
			label : '<?= $user->fullname() ?>',
			iconUrl : 'static/cms/img/16/user.png'
		});
	});
});
</script>

<div id ="<?= $uniqid ?>" class="page">
	<div class="line myBody">
		<div class="unit col c1"></div>
		<div class="unit col c7 ui-widget">
			<?= $fieldset_edit->open('admin/cms/user/form/edit/'.$user->user_id); ?>
			<?= $fieldset_edit->field('user_name')
				->set_template('{field}')
				->set_attribute('class', 'title c3');
			?>
			<?= $fieldset_edit->field('user_firstname')
				->set_template('{field}')
				->set_attribute('class', 'title c3');
			?>
			<div class="expander">
				<h3>Change details</h3>
				<div>
				<table>
					<?php
					foreach ($fieldset_edit->field() as $f) {
						if (in_array($f->name, array('user_name', 'user_firstname'))) {
							continue;
						}
						echo $f->build();
					}
					?>
				</table>
				</div>
			</div>
			<?= $fieldset_edit->close(); ?>

			<div class="expander">
				<h3>Change password</h3>
				<div>
				<?= $fieldset_password->open('admin/cms/user/form/edit/'.$user->user_id); ?>
					<table>
						<?php
						foreach ($fieldset_password->field() as $f) {
							echo $f->build();
						}
						?>
					</table>
				<?= $fieldset_password->close(); ?>
				</div>
			</div>

		</div>
		<div class="unit col c3"></div>
		<div class="unit lastUnit"></div>
	</div>
</div>
