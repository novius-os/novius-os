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
<h1><?= $logged_user->user_fullname; ?></h1>

<a href="admin/tray/account/disconnect">Disconnect</a>
<div id="tabs" style="width: 100%;">
    <ul style="width: 10%;">
        <li><a href="#password"><?= __('Password') ?></a></li>
        <li><a href="#display"><?= __('Display') ?></a></li>
    </ul>
    <div id="password" style="width: 85%;">
        <?= $fieldset_password ?>
    </div>
    <div id="display" style="width: 85%;">
        <?= $fieldset_display ?>
    </div>
</div>
<SCRIPT LANGUAGE="JAVAScript">
    require(['jquery-nos'], function($) {
        $(function() {
            //$.nos.media($('#background'), {mode: 'image'});
        });

        require(['static/cms/js/jquery/wijmo/js/jquery.wijmo.wijtabs.js'],
            function() {
                $('#tabs').wijtabs({
                    alignment: 'left'
                });
            }
        );
    });
</SCRIPT>