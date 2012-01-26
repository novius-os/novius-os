<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

    $id = uniqid('temp_');
?>
<div id="<?= $id ?>"></div>
<script type="text/javascript">
require([
        '<?= $urljson ?>',
		'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.mp3grid.js',
		'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.thumbnails.js',
        'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.nosgrid.js',
		'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.inspector-preview.js'
	], function( mp3Grid, $ ) {

        $.extend(mp3Grid.i18nMessages, <?= $i18n ?>);

		$(function() {
            var timeout,
                div = $('div#<?= $id ?>'),
                params = mp3Grid.build();

            if ($.isPlainObject(params.tab)) {
				try {
					$.nos.tabs.update(div, params.tab);
				} catch (e) {
					log('Could not update current tab. Maybe your config file should not try to update it.');
				}
            }

			$.nos.listener.add('mp3grid.<?= $id ?>', true, function() {
				div.removeAttr('id')
                .mp3grid(params.mp3grid)
                .parents('.nos-ostabs-panel')
                .bind('panelResize.ostabs', function(eventType, direct) {
                    if (direct) {
                        if (timeout) {
                            window.clearTimeout(timeout);
                        }
                        timeout = window.setTimeout(function() {
                            div.mp3grid('refresh');
                        }, 200);
                    } else {
                        div.mp3grid('refresh');
                    }
                });
				$.nos.listener.remove('mp3grid.<?= $id ?>', true, arguments.callee);
			})

			if (null == params.delayed || !params.delayed) {
				$.nos.listener.fire('mp3grid.<?= $id ?>', true, []);
			}
		});
	});
</script>
