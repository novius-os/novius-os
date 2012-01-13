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
<!--[if lt IE 7 ]> <html class="ie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class=""> <!--<![endif]-->
<head>
<?php
	if (isset($base)) {
		echo '<base href="'.$base.'" />';
	}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?= $title ?></title>
<meta name="robots" content="noindex,nofollow">
<link rel="shortcut icon" href="static/cms/img/noviusos.ico">
<?= $css ?>
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
<script src="<?= $require ?>" type="text/javascript"></script>
<?= $js ?>
</head>

<body>
	<?= $body ?>
</body>
</html>