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
<div id="noviusos" data-init-tabs="<?= $initTabs ?>" data-tray-tabs="<?= $trayTabs ?>" data-apps-tab="<?= $appsTab ?>" data-new-tab="<?= $newTab ?>"></div>
<script type="text/javascript">
require(['static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.ostabs'], function( $ ) {
		$(function() {
			var noviusos = $('#noviusos');
			noviusos.ostabs(noviusos.data());
		});
	});
</script>

