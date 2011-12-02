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
<input type="text" name="<?= htmlspecialchars($name) ?>" value="<?= $value ?>" />
<script type="text/javascript">
require([
		'jquery-nos'
	], function( $, undefined ) {
		$(function() {
			$('input[name=<?= $name ?>]').datepicker({
				showOn : 'both',
				buttonImage: 'static/cms/img/icons/date-picker.png',
				buttonImageOnly : true,
				autoSize: true,
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				showOtherMonths: true,
				selectOtherMonths: true,
				gotoCurrent: true,
				firstDay: 1,
				showAnim: 'slideDown'
			});
		});
	});
</script>