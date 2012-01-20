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
		$.nos.tabs.update({
			label : 'Add a new user',
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
			<?= $fieldset_add->open('admin/admin/user/form/add/'); ?>
			<div class="fieldset">
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

