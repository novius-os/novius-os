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
require([
        '<?= $urljson ?>',
		'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.mp3grid.js',
		'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.thumbnails.js',
        'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.nosgrid.js',
		'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.inspector-preview.js'
	], function( mp3Grid, $ ) {

        mp3Grid.i18n.load(<?= $i18n ?>);
        var params = mp3Grid.build();
		$(function() {
            if ($.isPlainObject(params.tab)) {
			    $.nos.tabs.updateTab(params.tab);
            }
			$('#mp3grid').mp3grid(params.mp3grid);
		});
	});
</script>
<div id="mp3grid"></div>
