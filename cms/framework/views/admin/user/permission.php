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
    require(['link!static/cms/css/permissions.css']);
</script>


<div class="permissions">

<form action="admin/cms/user/form/save_permissions" method="POST" id="<?= $uniqid = uniqid('id_') ?>">
  <input type="hidden" name="group_id" value="<?= $group->group_id ?>" />

	<div class="applications">
	    <div class="application all">
			<div class="maincheck">
				<input type="checkbox" name="access_to_everything" value="1"/>
			</div>
			<div class="infos">
				<?= __('Full access for everything') ?>
			</div>
	    </div>


<?php
foreach ($apps as $app => $perms) {

	\Config::load("$app::permissions", true);
    ?>

<input type="hidden" name="module[]" value="<?= $app ?>" />
    <div class="application item">
		<div class="maincheck">
			<input type="checkbox" name="access[<?= $app ?>]" value="1" <?= $group->check_permission($app, 'access') ? 'checked' : '' ?> />
		</div>
		<div class="icon">
			<?php
            if (!empty($apps[$app]['icon64'])) {
                echo '<img src="'.$apps[$app]['icon64'].'" />';
            }
            ?>
		</div>
		<div class="infos" title="<?= strtr(__('Application provided by {provider_name}'), array(
                '{provider_name}' => $apps[$app]['provider']['name'],
            )) ?>">
			<?= $apps[$app]['name'] ?>
		</div>
    </div>

    <div style="margin-left: 30px;">

	<?php
	/*
	$keys = \Config::get("$app::permissions", array());
	if (!empty($keys)) {
        foreach ($keys as $key => $value) {
            $driver = $group->get_permission_driver($app, $key);
            ?>
            <h2><?= $value['label']; ?></h2>
            <?php
            //\Debug::dump($driver);
            echo $driver->display($group);
        }
    }
    */
	?>

	</div>
<?php
}
?>
	</div>
    <div style="margin-top: 30px; margin-bottom: 20px; text-align: center;">
        <button type="submit" data-icon="check" style="font-size: 2em;"><?= __('Save the permissions') ?></button>
    </div>
</form>
</div>



<script type="text/javascript">
    require(["jquery"], function($) {

    	var $form = $('#<?= $uniqid ?>');

    	$.nos.ui.form($form);

    	$(".permissions .applications .application.item :input[type='checkbox']").unbind('change').change(function() {
			var $access_to_everything = $(this).closest('.applications').find(":input[name='access_to_everything']");
			var $all_checkboxes = $(this).closest('.applications').find(".application.item :input[type='checkbox']");
			var all_checked = true;
			$all_checkboxes.each(function() {
				if (!$(this).is(':checked')) {
					all_checked = false;
				}
			});

			$access_to_everything.attr('checked', all_checked);
			$access_to_everything.wijcheckbox('refresh');
    	}).change();

    	$(".permissions .applications :input[name='access_to_everything']").unbind('change').change(function() {
    		var $all_checkboxes = $(this).closest('.applications').find(".application.item :input[type='checkbox']");
			var all_checked = true;
			$all_checkboxes.each(function() {
				if (!$(this).is(':checked')) {
					all_checked = false;
				}
			});

			if (all_checked) {
				$all_checkboxes.attr('checked', false);
			} else {
				$all_checkboxes.attr('checked', true);
			}
			$all_checkboxes.wijcheckbox('refresh');
    	});

        $form.submit(function(e) {
            e.preventDefault();
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(json) {
                    $.nos.ajax.success(json);
                },
                error: function() {
                    $.nos.notify('An error occured', 'error');
                }
            });
        });

        $('h1 input:not(:checked)').closest('h1').next().hide();
        $('h1 input').change(function() {
            $(this).closest('h1').next()[$(this).is(':checked') ? 'show' : 'hide']();
            if (!$(this).is(':checked')) {
                $(this).closest('form').submit();
            }
        });


    });
</script>

