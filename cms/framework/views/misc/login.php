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
<div id="login">
	<img src="static/cms/img/logo.png" />
	<?php if (!empty($error)) { ?>
		<p class="ui-state-error">
			<span class="ui-icon ui-icon-alert" style="display:inline-block; vertical-align:middle;"></span> <?= $error ?>
		</p>
	<?php } ?>
	<form method="POST" action="">
		<p><label>Email:</label> <input type="email" name="email" id="email" value="<?= \Input::post('email', ''); ?>" /></p>
		<p><label>Password:</label> <input type="password" name="password" /></p>
		<p><input type="submit" value="Dive in"></p>
	</form>
	<script type="text/javascript">
	require(['jquery'], function($) {
		$(function() {
			$('#email').select();
		});
	});</script>
</div>