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
<script type="text/javascript">
    require(['jquery-nos'], function ($) {
        $(function () {
            $.nos.tabs.update({
                label : <?= \Format::forge()->to_json(isset($user) ? $user->fullname() : 'Add a user') ?>,
                iconUrl : 'static/cms/img/16/user.png'
            });
        });
    });
</script>


<style type="text/css">
/* ? */
/* @todo check this */
.ui-accordion-content-active {
	overflow: visible !important;
}
</style>

<?php
$fieldset->form()->set_config('field_template',  "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {error_msg}</td></tr>\n");

foreach ($fieldset->field() as $field) {
	if ($field->type == 'checkbox') {
		$field->set_template('{field} {label}');
	}
}
?>

<div class="page line ui-widget" id="<?= $uniqid = uniqid('id_'); ?>">
	<? /*<div class="unit col c1"></div>
	<div class="unit col c10" id="line_first" style="position:relative;"> */ ?>
        <div id="tabs" style="width: 92.4%; clear:both; margin:0 auto 1em;">
            <ul style="width: 15%;">
                <li><a href="#details"><?= __('User details') ?></a></li>
                <li><a href="#permissions"><?= __('Permissions') ?></a></li>
            </ul>
            <div id="details">
                <?= $fieldset->open('admin/cms/user/form/edit/'.$user->user_id); ?>
                <?= View::forge('form/layout_standard', array(
                    'fieldset' => $fieldset,
                    // Used by the behaviours (publishable, etc.)
                    'object' => $user,
                    'medias' => null,
                    'title' => array('user_firstname', 'user_name'),
                    'id' => 'user_id',

                    'save' => 'save',

                    'subtitle' => array(),

                    'content' => array(
                        \View::forge('form/expander', array(
                            'title'   => 'Details',
                            'nomargin' => false,
                            'content' => \View::forge('form/fields', array(
                                'fieldset' => $fieldset,
                                'fields' => array('user_email', 'user_last_connection'),
                            ), false)
                        ), false),
                        \View::forge('form/expander', array(
                            'title'   => 'Set a new password',
                            'nomargin' => false,
                            'content' => \View::forge('form/fields', array(
                                'fieldset' => $fieldset,
                                'fields' => array('password_reset', 'password_confirmation'),
                            ), false)
                        ), false),
                    ),
                ), false); ?>
                <?= $fieldset->close(); ?>
            </div>
            <div id="permissions">
               <?= $permissions ?>
            </div>
        </div>

    <? /* </div>
    <div class="unit lastUnit"></div> */ ?>
</div>

<script type="text/javascript">
    require(['jquery-nos'], function($) {
        $(function() {
            var $tabs = $('#tabs');
            $tabs.wijtabs({
                alignment: 'left'
            });
            $tabs.find('> ul').css({
                width : '15%'
            });

            $tabs.find('> div').css({
                width : '84%'
            });
            var $container = $('#<?= $uniqid ?>');
            var $password = $container.find('input[name=user_password]');

            <?php $formatter = \Format::forge(); ?>
            // Password strength
            require([
                'static/cms/js/vendor/jquery/jquery-password_strength/jquery.password_strength',
                'link!static/cms/js/vendor/jquery/jquery-password_strength/jquery.password_strength.css'
            ], function() {
                var strength_id = '<?= $uniqid ?>_strength';
                var $strength = $('<span id="' + strength_id + '"></span>');
                $password.after($strength);
                $password.password_strength({
                    container : '#' + strength_id,
                    texts : {
                        1 : ' <span class="color"></span><span class="box"></span><span class="box"></span><span class="box"></span> <span class="optional">' + <?= $formatter->to_json(__('Insufficient')) ?> + '</span>',
                        2 : ' <span class="color"></span><span class="color"></span><span class="box"></span><span class="box"></span> <span class="optional">' + <?= $formatter->to_json(__('Weak')) ?> + '</span>',
                        3 : ' <span class="color"></span><span class="color"></span><span class="color"></span><span class="box"></span> <span class="optional">' + <?= $formatter->to_json(__('Average')) ?> + '</span>',
                        4 : ' <span class="color"></span><span class="color"></span><span class="color"></span><span class="color"></span> <span class="optional">' + <?= $formatter->to_json(__('Strong')) ?> + '</span>',
                        5 : ' <span class="color"></span><span class="color"></span><span class="color"></span><span class="color"></span> <span class="optional">' + <?= $formatter->to_json(__('Outstanding')) ?> + '</span>'
                    }
                });
            });
        });
    });
</script>