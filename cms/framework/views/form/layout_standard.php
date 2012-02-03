
<script type="text/javascript">
require(['jquery-nos'], function($) {
	$.nos.ui.form("#<?= $uniqid1 = uniqid('id_') ?>,#<?= $uniqid2 = uniqid('id_') ?>");
});
</script>

<div class="line ui-widget" id="<?= $uniqid1 ?>">
	<div class="unit col c1"></div>
	<div class="unit col c7" id="line_first" style="position:relative;z-index:99;">
		<div class="line" style="margin-bottom:1em;">
			<?php
			foreach ((array) $medias as $field) {
				echo $fieldset->field($field)->build();
			}
			?>
			<?= $fieldset->field($title)/*->set_attribute('placeholder', $fieldset->field($title)->label)*/->set_template('{field}')->set_attribute('class', 'title c4')->build(); ?>
			<?php
			$value = $fieldset->field($id)->get_value();
			echo !empty($value) ? $fieldset->field($id)->set_template('{label} {field}')->build() : '';
			?>
		</div>
		<div class="line" style="margin-bottom:1em;overflow:visible;">
			<?php
			foreach ((array) $subtitle as $field) {
				echo '<div class="unit col">'.$fieldset->field($field)->build().'</div>';
			}
			?>
		</div>
	</div>
	<div class="unit col c3" style="position:relative;z-index:98;text-align:center;">
		<p style="margin: 0 0 1em;"><?= $fieldset->field($published)->set_template('{field} {label}')->build() ?></p>
		<p><?= $fieldset->field($save)->set_template('{field}')->build() ?> &nbsp; or &nbsp; <a href="#" onclick="javascript:$.nos.tabs.close();return false;">Cancel</a></p>
	</div>
</div>

<div class="line ui-widget" id="<?= $uniqid2 ?>">
	<div class="unit col c1"></div>
	<div class="unit col c7" id="line_second" style="position:relative;margin-bottom:1em;">
		<?= $content ?>
	</div>

	<?php
	$fieldset->form()->set_config('field_template',  "\t\t<span class=\"{error_class}\">{label}{required}</span>\n\t\t<br />\n\t\t<span class=\"{error_class}\">{field} {error_msg}</span>\n");
	?>
	<div class="unit col c3" style="position:relative;z-index:98;margin-bottom:1em;">
		 <div class="accordion">
			<?php
			foreach ((array) $menu as $title => $fields) {
				?>
				<h3><a href="#"><?= $title ?></a></h3>
				<div style="overflow:visible;">
					<?php
					foreach ((array) $fields as $field) {
						try {
							echo '<p>'.$fieldset->field($field)->build().'</p>';
						} catch (\Exception $e) {
							throw new \Exception("Field $field : " . $e->getMessage(), $e->getCode(), $e);
						}
					}
					?>
				</div>
				<?php
			}
			?>
		 </div>
	 </div>
	<div class="unit lastUnit"></div>
</div>