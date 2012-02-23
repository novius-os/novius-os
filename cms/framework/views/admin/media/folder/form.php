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
<div class="fieldset standalone" id="<?= $id = uniqid('id_') ?>">
    <?php
    $fieldset->set_config('field_template', '{label}{required} {field} {error_msg}');

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
    <?= $fieldset->open('admin/admin/media/folder/do'); ?>
    <?= $fieldset->field('medif_parent_id')->build(); ?>
    <p><?= $fieldset->field('medif_title')->build(); ?></p>
    <p><?= $fieldset->field('medif_path')->build(); ?><span class="optional"><?= __('Optional') ?></span></p>
    <p><?= $fieldset->field('save')->build(); ?></p>
    <?= $fieldset->close(); ?>
</div>

<script type="text/javascript">
require(['jquery-nos', 'static/cms/js/jquery/jquery-form/jquery.form.min'], function($) {
    $.nos.ui.form('#<?= $id ?>');
    $(function() {
        $('form').submit(function(e) {
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(json) {
                    console.log(json);
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
    });
});
</script>