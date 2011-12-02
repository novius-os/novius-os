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
<a href="/admin/tray/plugins">RELOAD</a>
<script type="text/javascript">
require(['jquery-nos'], function ($nos) {
	$nos(function() {
		<?php
		$flash = \Session::get_flash('notification.plugins');
		if (!empty($flash)) {
			?>
				$nos.nos.notify(<?= \Format::forge()->to_json($flash); ?>);
			<?php
		}
		?>
	});
});
</script>
<h1><?= Cms\Gettext::cms('Plugins management'); ?></h1>

<style type="text/css">
  table.borderized {
    border-collapse: collapse;
  }
  table.borderized td, table.borderized th {
    border: 1px solid #000;
    padding: 0 0.5em;
  }
  table.borderized ul {
    padding-left: 20px;
  }
</style>

  <h2><?= Cms\Gettext::cms('Installed') ?></h2>
  
  <?php if (empty($installed)) { ?>
  <em><?php echo Cms\Gettext::cms('No plugins found') ?>.</em>
  <?php } ?>
  
  <?php foreach ($installed as $app => $metadata) { ?>
    <li>
		<?= isset($metadata['name']) ? $metadata['name'] : $app ?>
		[<a href="admin/tray/plugins/remove/<?= $app ?>">remove</a>]
		<?= !empty($metadata['dirty']) ? 'PROBLEM [<a href="admin/tray/plugins/add/'.$app.'">repair install</a>]' : '' ?>
	</li>
  <?php } ?>

  
  <h2><?= Cms\Gettext::cms('Available') ?></h2>
  
  <?php if (empty($others)) { ?>
  <em><?= Cms\Gettext::cms('No plugins found') ?></em>
  <?php } ?>
  
  <?php foreach ($others as $app => $metadata) { ?>
    <li><?= isset($metadata['name']) ? $metadata['name'] : $app ?> [<a href="admin/tray/plugins/add/<?= $app ?>">add</a>]</li>
  <?php } ?>

<h2><?= Cms\Gettext::cms('Install from a .zip file') ?></h2>

<form method="post" action="/admin/tray/plugins/upload" enctype="multipart/form-data">
	<input type="file" name="zip" />
	<input type="submit" value="Upload this module" />
</form>