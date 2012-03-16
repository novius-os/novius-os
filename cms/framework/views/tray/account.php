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
<div class="page line ui-widget" id="<?= $uniqid = uniqid('id_'); ?>">
	<div class="unit col c1"></div>
	<div class="unit col c10" id="line_first" style="position:relative;;">
		<div class="line" style="overflow:visible;">

			<h1 class="title" style="float:left;"><?= $logged_user->fullname(); ?></h1>

			<a style="float:right;overflow:auto;" href="admin/cms/tray/account/disconnect">
				<button data-icon="power"><?= __('Disconnect') ?></button>
			</a>

			<div id="tabs" style="width: 100%; clear:both; margin-top:3em;">
				<ul style="width: 15%;">
					<li><a href="#infos"><?= __('Your account') ?></a></li>
					<li><a href="#password"><?= __('Change password') ?></a></li>
					<li><a href="#display"><?= __('Theme') ?></a></li>
				</ul>
				<div id="infos" style="width: 80%;">
					<?= $fieldset_infos ?>
				</div>
				<div id="password" style="width: 80%;">
					<?= $fieldset_password ?>
				</div>
				<div id="display" style="width: 80%;">
					<?= $fieldset_display ?>
				</div>
			</div>
		</div>
	</div>
	<div class="unit lastUnit"></div>
</div>

<script type="text/javascript">
    require(['jquery-nos'], function($) {
		$.nos.ui.form('#<?= $uniqid ?>');
		$(function() {
			$('#<?= $fieldset_display->form()->get_attribute('id') ?>').bind('ajax_success', function(e, json) {
				if (json.wallpaper_url) {
					$('#noviusospanel').css('background-image', 'url("' + json.wallpaper_url + '")');
				} else {
					$('#noviusospanel').css('background-image', '');
				}
			});
		});

        $('#tabs').wijtabs({
            alignment: 'left'
        });
    });
</script>