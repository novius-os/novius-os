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
    <p><?php
    if ($usage_count == 0) {
        ?>
        <p><?= __('The media is not used anywhere and can be safely deleted.') ?></p>
        <p><?= __('Please confirm the suppression below.') ?></p>
        <?php
    } else {
        ?>
        <p><?= strtr(__('The media is used <strong>{count} times</strong> by your applications.'), array(
            '{count}' => $usage_count,
        )) ?></p>
        <p><?= __('To confirm the supression, you need to enter this number in the field below') ?></p>
        <p><?= strtr(__('Yes, I want to delete all {count} usage of the media.'), array(
            '{count}' => '<input data-id="verification" data-verification="'.$usage_count.'" size="'.(strlen($usage_count) + 1).'" />',
        )); ?></p>
        <?php
    }
    ?></p>
    <p>
        <button data-icon="trash" data-id="confirmation"><?= __('Confirm the supression') ?></button>
        &nbsp; <?= __('or') ?> &nbsp;
        <a href="#" data-id="cancel"><?= __('Cancel') ?></a>
    </p>
</div>

<script type="text/javascript">
require(['jquery-nos'], function($) {
    $.nos.ui.form('#<?= $uniqid ?>');
    $(function() {
        var $container    = $('#<?= $uniqid ?>');
        var $verification = $container.find('input[data-id=verification]');
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
            if ($verification.length && $verification.val() != $verification.data('verification')) {
                $.nos.notify(<?= \Format::forge()->to_json(__('Wrong confirmation')); ?>, 'error');
                return;
            }
            $.nos.ajax.request({
                url : 'admin/admin/media/actions/delete_media_confirm',
                method : 'POST',
                data : {
                    id : <?= $media->media_id ?>
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