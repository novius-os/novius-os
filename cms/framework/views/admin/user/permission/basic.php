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
<?php
foreach ($permissions as $permission) {
    $checked = $group->check_permission($app, $permission['key']);
    ?>
    <li><label><input type="checkbox" name="app[<?= $app; ?>][]" value="<?= $permission['key']; ?>" <?= $checked ? 'checked' : ''; ?> /> <?= $permission['title']; ?></label></li>
    <?php
}
?>