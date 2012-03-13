<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
if (empty($publishable)) {
    $publishable = $object->behaviors('Cms\Orm_Behaviour_Publishable');
}

if (empty($publishable)) {
    return;
}
?>
<p style="margin: 0 0 1em;">
    <?php $published = !empty($object) ? $object->published() : false; ?>
    <table style="margin:0 2em;">
        <tr>
            <td id="<?= $buttonset = uniqid('buttonset_') ?>" class="publishable" style="width:63px; text-align:right;">
                <input type="radio" name="<?= $publishable['publication_bool_property'] ?>" value="0" id="<?= $uniqid_no = uniqid('no_') ?>" <?= $published === false ? 'checked' : ''; ?> /><label for="<?= $uniqid_no ?>"><img src="static/cms/img/icons/status-red.png" /></label>
                <input type="radio" name="<?= $publishable['publication_bool_property'] ?>" value="1" id="<?= $uniqid_yes = uniqid('yes_') ?>" <?= $published === true ? 'checked' : ''; ?> /><label for="<?= $uniqid_yes ?>"><img src="static/cms/img/icons/status-green.png" /></label>
            </td>
            <td style="padding-left:10px;" id="<?= $label = uniqid('label_') ?>"></td>
        </tr>
    </table>
</p>

<script type="text/javascript">
require(['jquery-nos'], function($) {
    <?php
    $formatter = \Format::forge();
    ?>
    var labels = {
        'undefined' : {
            0 : <?= $formatter->to_json('Will not be published') ?>,
            1 : <?= $formatter->to_json('Will be published') ?>
        },
        'no' : {
            0 : <?= $formatter->to_json('Not published') ?>,
            1 : <?= $formatter->to_json('Will be published') ?>
        },
        'yes' : {
            0 : <?= $formatter->to_json('Will be unpublished') ?>,
            1 : <?= $formatter->to_json('Published') ?>
        }
    };

    var initial_status = '<?= empty($object) ? 'undefined' : ($published ? 'yes' : 'no') ?>';

    $(function() {
        var $buttonset = $('#<?= $buttonset ?>');
        var $label     = $('#<?= $label ?>');

        $buttonset.buttonset({
            text : false,
            icons : {
                primary:'ui-icon-locked'
            }
        });
        $buttonset.find(':radio').change(function() {
            $label.text(labels[initial_status][$(this).val()]);
        })
        $buttonset.find(':checked').triggerHandler('change');

        $buttonset.closest('form').bind('ajax_success', function(e, json) {
            if (json.publication_initial_status == null) {
                log('Potential error: publication_initial_status in JSON response.');
                return;
            }
            initial_status = json.publication_initial_status == 1 ? 'yes' : 'no';
            $buttonset.find(':checked').triggerHandler('change');
        });
    });
});
</script>