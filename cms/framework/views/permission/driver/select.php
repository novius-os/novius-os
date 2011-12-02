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
<ul>
<?php
foreach ($choices as $key => $choice) {
    $checked = $driver->check($group, $key);
    ?>
    <li><label><input type="checkbox" name="permission[<?= $module ?>][<?= $identifier ?>][]" value="<?= $key; ?>" <?= $checked ? 'checked' : ''; ?> /> <?= $choice['title']; ?></label></li>
    <?php
}
?>
</ul>