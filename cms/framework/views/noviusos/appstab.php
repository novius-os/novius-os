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
<div id="switcher"></div>
<script type="text/javascript">
(function($) {
	$(function() {
		//$('#switcher').themeswitcher();
        $('#apps').sortable({
            update: function() {
                orders = {};
                $('.app').each(function(i) {
                    orders[$(this).data('key')] = {order: i};
                });
                $.nos.saveUserConfiguration('misc.apps', orders);
            }
        });
	});
})(jQuery);
</script>
<div align="center">
<form data-ui="ajaxForm" id="search">
	<span id="magnifier"></span>
	<input type="search" name="search" placeholder="Search" data-button-go="false" />
</form>
</div>

<link rel="stylesheet" type="text/css" href="static/cms/css/home.css"></link>

<div id="apps">
	<?php
	foreach ($apps as $app) {
	?>
	<a class="app" href="<?= $app['href'] ?>" data-key="<?= $app['key'] ?>">
		<span class="icon">
			<img class="gloss" src="static/cms/img/64/gloss.png" />
			<img width="64" src="<?= $app['icon64'] ?>" />
		</span>
		<span class="text"><?= $app['name'] ?></span>
	</a>
	<?php
	}
	?>
</div>

<script type="text/javascript">
require(['jquery-nos'], function($) {
	$('a.app').click(function(e) {
		$(this).attr('href') == '#' || $.nos.tabs.add({
			url: this.href,
			app: true,
			iconSize: 32,
			labelDisplay: false
		}, true);
		e.preventDefault();
	});
});
</script>