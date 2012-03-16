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
    <input type="hidden" name="id" value="<?= $page->page_id ?>" />
    <p><?php
    $children       = $page->find_children_recursive(false);
    $children_count = count($children);

    $page_langs = $page->find_lang('all');
    $lang_count = count($page_langs);

    $locales = \Config::get('locales', array());
    $languages_list = array();
    foreach ($page_langs as $page) {
        $languages_list[] = \Arr::get($locales, $page->get_lang(), $page->get_lang());
    }

    if ($children_count == 0 && $lang_count == 1) {
        ?>
        <p><?= __('This page has no children and can be safely deleted.') ?></p>
        <p><?= __('Please confirm the suppression below.') ?></p>
        <?php
    } else {
        if ($children_count > 0) {
            ?>
            <p><?= strtr(__(
                    $children_count == 1 ? 'The page contains <strong>one child</strong>.'
                                       : 'The page contains <strong>{count} children</strong>.'
            ), array(
                '{count}' => $children_count,
            )) ?></p>
            <p><?= __('To confirm the deletion, you need to enter this number in the field below') ?></p>
            <p><?= strtr(__('Yes, I want to delete the page and all of its {count} children.'), array(
                '{count}' => '<input data-id="verification1" data-verification="'.$children_count.'" size="'.(strlen($children_count) + 1).'" />',
            )); ?></p>
            <?php
        }
        if ($lang_count > 1) {
            ?>
            <p><?= strtr(__('The page exists in <strong>{count} languages</strong>.'), array(
                '<strong>' => '<strong title="'.implode(', ', $languages_list).'">',
                '{count}' => $lang_count,
            )) ?></p>
            <?= __('Delete the page in the following languages: ') ?>
            <select name="lang">
                <option value="all"><?= __('All languages') ?></option>
                <?php
                foreach ($page_langs as $page) {
                    ?>
                        <option value="<?= $page->get_lang() ?>"><?= \Arr::get($locales, $page->get_lang(), $page->get_lang()); ?></option>
                    <?php
                }
                ?>
            </select>
            <?php
        }
    }
    ?></p>
    <p>
        <button type="submit" class="primary ui-state-error" data-icon="trash" data-id="confirmation"><?= __('Confirm the deletion') ?></button>
        &nbsp; <?= __('or') ?> &nbsp;
        <a href="#" data-id="cancel"><?= __('Cancel') ?></a>
    </p>
</div>

<script type="text/javascript">
require(['jquery-nos'], function($) {
    $.nos.ui.form('#<?= $uniqid ?>');
    $(function() {
        var $container     = $('#<?= $uniqid ?>');
        var $verification1 = $container.find('input[data-id=verification1]');
        var $verification2 = $container.find('input[data-id=verification2]');
        var $confirmation  = $container.find('button[data-id=confirmation]');

        var $dialog = $container.closest(':wijmo-wijdialog');
        var closeDialog = function() {
            $dialog && $dialog
                .wijdialog('close')
                .wijdialog('destroy')
                .remove();
        }

        // Create a form so we can retrieve its data with jQuery.serialize()
        $container.wrapInner('<form></form>');
        $confirmation.click(function(e) {
            e.preventDefault();
            if ($verification1.length && $verification1.val() != $verification1.data('verification')) {
                $.nos.notify(<?= \Format::forge()->to_json(__('Wrong confirmation')); ?>, 'error');
                return;
            }
            if ($verification2.length && $verification2.val() != $verification2.data('verification')) {
                $.nos.notify(<?= \Format::forge()->to_json(__('Wrong confirmation')); ?>, 'error');
                return;
            }

            $.nos.ajax.request({
                url : 'admin/cms/page/page/delete_page_confirm',
                method : 'POST',
                data : $container.find('form').serialize(),
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