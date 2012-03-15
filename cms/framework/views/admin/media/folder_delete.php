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
    if ($media_count == 0) {
        ?>
        <p><?= __('The folder is empty and can be safely deleted.') ?></p>
        <p><?= __('Please confirm the suppression below.') ?></p>
        <?php
    } else {
        ?>
        <p><?= strtr(__(
                $media_count == 1 ? 'There are <strong>one media</strong> in this folder.'
                                  : 'There is <strong>{count} medias</strong> in this folder.'
        ), array(
            '{count}' => $media_count,
        )) ?></p>
        <p><?= __('To confirm the deletion, you need to enter this number in the field below') ?></p>
        <p><?= strtr(__('Yes, I want to delete all {count} files from the media centre.'), array(
            '{count}' => '<input data-id="verification" data-verification="'.$media_count.'" size="'.(strlen($media_count) + 1).'" />',
        )); ?></p>
        <?php
    }
    ?></p>
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
        var $verification = $container.find('input[data-id=verification]');
        var $confirmation = $container.find('button[data-id=confirmation]');

        var $dialog = $container.closest(':wijmo-wijdialog');
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
                url : 'admin/admin/media/actions/delete_folder_confirm',
                method : 'POST',
                data : {
                    id : <?= $folder->medif_id ?>
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