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
<div id="<?= $uniqid = uniqid('id_') ?>">
    <?php
    $fieldset->set_config('field_template', '{field}');

    foreach ($fieldset->field() as $field) {
        if ($field->type == 'submit') {
            $field->set_template('{field}');
        }
        if ($field->type == 'file') {
            $form_attributes = $fieldset->get_config('form_attributes', array());
            $form_attributes['enctype'] = 'multipart/form-data';
            $fieldset->set_config('form_attributes', $form_attributes);
        }
    }
    ?>
    <?= $fieldset->open('admin/admin/media/folder/do_edit'); ?>
    <?= $fieldset->field('medif_id')->build(); ?>
    <table class="fieldset">
        <tr>
            <th><?= $fieldset->field('medif_title')->label; ?></th>
            <td><?= $fieldset->field('medif_title')->build(); ?></td>
        </tr>
        <tr style="height:85px;">
            <th style="vertical-align: top;"><?= $fieldset->field('medif_path')->label; ?></th>
            <td style="width:350px;vertical-align: top;">
                <label><input type="checkbox" data-id="same_title" <?= $checked ? 'checked' : '' ?>> <?= __('Generate from title') ?></label> <br />
                <span style="vertical-align:middle;">
                    http://yoursite.com/media/<span data-id="path_prefix"><?= $folder->parent->medif_path ?></span>
                </span>
                <?= $fieldset->field('medif_path')->build(); ?>
            </td>
        </tr>
    </table>
    <p>
        <?= $fieldset->field('save')->build(); ?>
        &nbsp; <?= __('or') ?> &nbsp;
        <a href="#" data-id="cancel"><?= __('Cancel') ?></a>
    </p>
    <?= $fieldset->close(); ?>
</div>

<script type="text/javascript">
require(['jquery-nos', 'static/cms/js/vendor/jquery/jquery-form/jquery.form.min'], function($) {
    $.nos.ui.form('#<?= $uniqid ?>');
    $(function() {

        var $container = $('#<?= $uniqid ?>');

		var $title      = $container.find('input[name=medif_title]');
		var $seo_title  = $container.find('input[name=medif_path]');
		var $same_title = $container.find('input[data-id=same_title]');

        var $dialog       = $.nos.data('dialog');
        //var $dialog       = window.parent.jQuery(':wijmo-wijdialog:last');
        var closeDialog = function() {
            $dialog && $dialog
                .wijdialog('close')
                .wijdialog('destroy')
                .remove();
        }

		// Same title and description (alt)
		$title.bind('change keyup', function() {
			if ($same_title.is(':checked')) {
				$seo_title.val(seo_compliant($title.val()));
			}
		});
		$same_title.change(function() {
			if ($(this).is(':checked')) {
				$seo_title.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
                $title.triggerHandler('change');
			} else {
				$seo_title.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
			}
		}).triggerHandler('change');

        $container.find('form').submit(function(e) {
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(json) {
                    if (json.closeDialog) {
                        window.parent.jQuery(':wijmo-wijdialog')
                            .wijdialog('close')
                            .wijdialog('destroy')
                            .remove();
                    }
                    $.nos.ajax.success(json);
                },
                error: function() {
                    $.nos.notify('An error occured', 'error');
                }
            });
            e.preventDefault();
        });

        $container.find('a[data-id=cancel]').click(function(e) {
            e.preventDefault();
            closeDialog();
        });
    });
});

<?php
include __DIR__.'/seo_compliant.js';
?>
</script>