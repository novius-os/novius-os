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

<h1><?= strtr(__('Modify permissions for {username}'), array(
    '{username}' => !empty($user) ? $user->fullname() : $group->group_name,
)) ?></h1>

<form action="admin/cms/user/form/save_permissions" method="POST" id="<?= $uniqid = uniqid('id_') ?>">
  <input type="hidden" name="group_id" value="<?= $group->group_id ?>" />

<?php
foreach ($apps as $app => $perms) {

	\Config::load("$app::permissions", true);
    ?>
    <h1 title="Application provided by <?= $apps[$app]['provider']['name']; ?>"><label><input type="checkbox" name="access[<?= $app ?>]" value="1" <?= $group->check_permission($app, 'access') ? 'checked' : '' ?> /> <?= $apps[$app]['name']; ?></label></h1>
    <div style="margin-left: 30px;">
		<input type="hidden" name="module[]" value="<?= $app ?>" />
	<?php
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
	?>
	   <input type="submit" value="Modify the permissions of <?= $apps[$app]['name']; /*?> <?= !empty($user) ? 'user '.$user->fullname() : $group->group_name */ ?>">
	</div>
<?php
}
?>
</form>

<script type="text/javascript">
    require(["jquery"], function($) {

        var $form = $('#<?= $uniqid ?>');

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

