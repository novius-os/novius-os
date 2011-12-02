/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
$(document).ready(function() {

    $('a[href$="reso.htm"]').each(function() {
        $(this).attr('href', $(this).attr('href') + '?iframe').orangeBox({
            iframeWidth : 1040,
            iframeHeight : 631,
            addThis : false
        });
        if (window.location.hash == '#play') {
            $(this).click();
        }
    });
});