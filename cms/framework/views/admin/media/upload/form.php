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
    <?= $fieldset->open('admin/admin/media/upload/do'); ?>
    <? /*
    <table width="100%">
        <tr>
            <td style="width:50%; height:300px;">
                <div style="width:100%; height:100%;border: 1px solid red;">
                    <?= $fieldset->field('folder_inspector')->build(); ?>
                </div>
            </td>
            <td style="width:50%; vertical-align:top;">
     */ ?>
                <?= $fieldset->field('media_path_id')->build(); ?>
                <p><?= $fieldset->field('media')->build(); ?></p>
                <p><?= $fieldset->field('media_title')->build(); ?><span class="optional"><?= __('Optional') ?></span></p>
<?php /*
            </td>
        </tr>
    </table>
 */ ?>
    <p><?= $fieldset->field('save')->build(); ?></p>
    <?= $fieldset->close(); ?>
</div>


<script type="text/javascript">
require([
    'jquery-nos',
    'static/cms/js/jquery/jquery-form/jquery.form.min'
],
function($) {
    $.nos.ui.form('#<?= $id ?>');
    $(function() {
        $('form').submit(function(e) {
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(json) {
                    //Success before close, success use the window reference
                    $.nos.ajax.success(json);
                    if (json.closeDialog) {
                        window.parent.jQuery(':wijmo-wijdialog:last')
                            .wijdialog('close')
                            .wijdialog('destroy')
                            .remove();
                    }
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