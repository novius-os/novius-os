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
			label : '<?= $user->user_fullname ?>',
			iconUrl : 'static/modules/cms_blog/img/16/author.png'
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
		$('.fieldset').wijexpander({expanded: true });
	});
});
</script>

<div class="page myPage">
	<div class="line myBody">
		<div class="unit col c1"></div>
		<div class="unit col c7 ui-widget">
			<?= $fieldset_edit->open('admin/user/form/edit/'.$user->user_id); ?>
			<?= $fieldset_edit->field('user_fullname')
				->set_template('{field}')
				->set_attribute('class', 'title c4');
			?>
			<div class="fieldset">
				<h3>Change details</h3>
				<div>
				<table>
					<?php
					foreach ($fieldset_edit->field() as $f) {
						if ($f->name == 'user_fullname') {
							continue;
						}
						echo $f->build();
					}
					?>
				</table>
				</div>
			</div>
			<?= $fieldset_edit->close(); ?>
			
			
			<div class="fieldset">
				<h3>Change password</h3>
				<div>
				<?= $fieldset_password->open('admin/user/form/edit/'.$user->user_id); ?>
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
