

<script type="text/javascript">
require(['jquery-nos'], function($) {
	$(function() {
		$(":input[type='text'],:input[type='password'],textarea").wijtextbox();
		$(":input[type='submit'],button").button();
		$("select").wijdropdown();
		$(":input[type=checkbox]").wijcheckbox();
		$('.expander').wijexpander({expanded: true });
		$('.accordion').wijaccordion({
			header: "h3"
		});
	});
});
</script>

<div class="line ui-widget">
	<div class="unit col c1"></div>
	<div class="unit col c7" id="line_first" style="position:relative;z-index:99;">
		<div class="line" style="margin-bottom:1em;">
			<?php
			foreach ((array) $medias as $field) {
				echo $fieldset->field($field)->build();
			}
			?>
			<?= $fieldset->field($title)->set_template('{field}')->set_attribute('class', 'title c4')->build(); ?>
			<?= $fieldset->field($id)->set_template('{label} {field}')->build(); ?>
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

<div class="line ui-widget">
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
						echo '<p>'.$fieldset->field($field)->build().'</p>';
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