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
<!DOCTYPE html>
<html>
<head>
	<base href="<?php echo Uri::base(false) ?: 'http'.(Input::server('HTTPS') ? 's' : '').'://'.Input::server('HTTP_HOST') ?>" />
	<meta charset="utf-8">
	<title>Novius OS</title>
	<style type="text/css">
		* { margin: 0; padding: 0; }
		body { background-color: #EEE; font-family: sans-serif; font-size: 16px; line-height: 20px; margin: 40px; }
		#wrapper { padding: 30px; background: #fff; color: #333; margin: 0 auto; width: 800px; }
		a { color: #36428D; }
		h1 { color: #000; font-size: 45px; padding: 0 0 25px; line-height: 1em; }
		p { margin: 0 0 15px; line-height: 22px;}
		.wip img { vertical-align: middle; }
	</style>
	<script type="text/javascript">
		function fuel_toggle(elem){elem = document.getElementById(elem);if (elem.style && elem.style['display']){var disp = elem.style['display'];}else if (elem.currentStyle){var disp = elem.currentStyle['display'];}else if (window.getComputedStyle){var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');}elem.style.display = disp == 'block' ? 'none' : 'block';return false;}
	</script>
	<script type="text/javascript">
		var require = {
			paths: {
				'jquery-nos': 'static/cms/js/nos',
				'jquery': 'static/cms/js/jquery/jquery-1.7.1.min',
				'jquery-ui' : 'static/cms/js/jquery/jquery-ui/jquery-ui-1.8.17.custom.min',
				'link': 'static/cms/js/requirejs/link',
				'order': 'static/cms/js/requirejs/order.min',
				'domReady': 'static/cms/js/requirejs/domReady.min'
			},
			jQuery: '1.7.1',
			catchError: true,
			priority: ['jquery'],
			deps: [
				'jquery-ui',
				'jquery-nos',
				'static/cms/js/log'
			]
		};
	</script>
	<script src="static/cms/js/requirejs/require.js" type="text/javascript"></script>
	<script type="text/javascript">
	require(['jquery-nos'], function($) {
		$(function() {
			$.nos.tabs.updateTab({
				label : 'Not implemented yet'
			});
		});
	});
	</script>
</head>
<body>
	<div id="wrapper">
		<h1>404 Not Found</h1>
		<img src="static/cms/img/logo.png" style="float:left; margin-right: 30px;"/>
		<br /><br /><br />
		<p class="wip">
			<img src="static/cms/img/flags/fr.png" />&nbsp; Cette fonction n'est pas encore implémentée. Revenez bientôt !
		</p>
		<br />
		<p class="wip">
			<img src="static/cms/img/flags/gb.png" />&nbsp; This feature is not implemented yet. Check again soon!
		</p>
		<br style="clear:left;" />

		<p class="intro"></p>
	</div>
</body>
</html>