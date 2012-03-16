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
<div id="<?= $uniqid = uniqid('id_') ?>" class="fieldset standalone">

    <p><?= __('Deleting a user is permanent, there is no undo.') ?></p>
    <p><?= __('Please confirm the deletion:'); ?></p>
    <p>
        <button class="primary ui-state-error" data-icon="trash" data-id="confirmation"><?= __('Confirm the deletion') ?></button>
        &nbsp; <?= __('or') ?> &nbsp;
        <a href="#" data-id="cancel"><?= __('Cancel') ?></a>
    </p>
</div>

<script type="text/javascript">
require(['jquery-nos'], function($) {
    $.nos.ui.form('#<?= $uniqid ?>');
    $(function() {
        var $container    = $('#<?= $uniqid ?>');
        var $confirmation = $container.find('button[data-id=confirmation]');

        var $dialog       = $.nos.data('dialog');
        //var $dialog       = window.parent.jQuery(':wijmo-wijdialog:last');
        var closeDialog = function() {
            $dialog && $dialog
                .wijdialog('close')
                .wijdialog('destroy')
                .remove();
        }

        $confirmation.click(function(e) {
            e.preventDefault();
            $.nos.ajax.request({
                url : 'admin/cms/user/user/delete_user_confirm',
                method : 'POST',
                data : {
                    id : <?= $user->user_id ?>
                },
                success : function(json) {
                    closeDialog();
                }
            });
        });

        $container.find('a[data-id=cancel]').click(function(e) {
            e.preventDefault();
            closeDialog();
        });

    });
});
</script>