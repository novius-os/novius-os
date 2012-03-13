<div class="expander fieldset" style="margin-bottom:1.5em;" <?= !empty($options) ? 'data-wijexpander-options="'.htmlspecialchars(Format::forge()->to_json($options)).'"' : '' ?>>
	<h3><?= $title ?></h3>
	<div style="overflow:visible;<?= isset($nomargin) ? 'margin:0;padding:0;' : '' ?>">
		<?= $content ?>
	</div>
</div>