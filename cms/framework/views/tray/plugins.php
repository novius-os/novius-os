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
<style type="text/css">
	.app_list {
		width : 400px;
		margin: 1em 0 0;
	}
</style>

<div class="page line ui-widget" id="<?= $uniqid = uniqid('id_'); ?>">
	<div class="unit col c1"></div>
	<div class="unit col c10" id="line_first" style="position:relative;;">
		<div class="line" style="overflow:visible;">
			<h1 class="title"><?= Cms\I18n::get('Applications'); ?></h1>

			<div class="app_list">
				<table>
					<thead>
						<tr>
							<td><?= Cms\I18n::get('Installed and ready to use') ?></td>
							<td><?= __('Actions') ?></td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($installed as $app => $metadata) { ?>
						<tr>
							<td><?= isset($metadata['name']) ? $metadata['name'] : $app ?></td>
							<td>
								<a href="admin/cms/tray/plugins/remove/<?= $app ?>">remove</a>
								<?= !empty($metadata['dirty']) ? '- [<a href="admin/cms/tray/plugins/add/'.$app.'">repair install</a>]' : '' ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				<?php if (empty($installed)) { ?>
				<em><?php echo Cms\I18n::get('No applications found') ?>.</em>
				<?php } ?>
			</div>

			<p>&nbsp;</p>

			<div class="app_list">
				<table>
					<thead>
						<tr>
							<td><?= Cms\I18n::get('Available for installation') ?></td>
							<td><?= __('Actions') ?></td>
						</tr>
					</thead>
					<tbody>
				<?php foreach ($others as $app => $metadata) { ?>
						<tr>
							<td><?= isset($metadata['name']) ? $metadata['name'] : $app ?> </td>
							<td><a href="admin/cms/tray/plugins/add/<?= $app ?>">add</a></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				<?php if (empty($others)) { ?>
				<em><?= Cms\I18n::get('No applications found') ?></em>
				<?php } ?>

			</div>

			<?php if ($allow_upload) { ?>

			<p>&nbsp;</p>
			<h1 class="title"><?= Cms\I18n::get('Install from a .zip file') ?></h1>

			<form method="post" action="/admin/cms/tray/plugins/upload" enctype="multipart/form-data">
				<input type="file" name="zip" />
				<input type="submit" value="Upload the application" />
			</form>
			<?php } ?>
		</div>
	</div>
	<div class="unit lastUnit"></div>
</div>

<script type="text/javascript">
	require(['jquery-nos'], function ($) {
		$.nos.ui.form('#<?= $uniqid ?>');
		$(".app_list table").wijgrid({
			columns: [
				{  },
				{ width: 100, ensurePxWidth: true }
			] });

		<?php
		$flash = \Session::get_flash('notification.plugins');
		if (!empty($flash)) {
			?>
				$.nos.notify(<?= \Format::forge()->to_json($flash); ?>);
			<?php
		}
		?>
	});
</script>