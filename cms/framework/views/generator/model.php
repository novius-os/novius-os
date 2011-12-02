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
<h1>Génération de classe Model</h1>
<form method="post" action="<?= $url.'/submit' ?>">
	<h2>Propriété de votre Model</h2>
	<div><label>Namespace votre model : <input type="text" name="namespace"></label></div>
	<div><label>Sous répertoire de votre model : <input type="text" name="subdir"></label></div>
	<div><label>Nom de votre model : <input type="text" name="name"></label></div>
	<div>
	<label>Table sur laquelle votre Model sera mappé <select name="table">
		<option value="">Choisissez une table</option>
<?php
	foreach ($tables as $item) {
?>
		<option value="<?= $item ?>"><?= $item ?></option>
<?php
	}
?>
	</select></label>
	</div>
	<br />

	<div id="relationships" style="display:none;">
	<h2>Les relations de votre Model</h2>
	<table width="100%">
	<tr>
		<th>Nom</th>
		<th>Model</th>
		<th>Type</th>
		<th>Relation</th>
		<th>Supp.</th>
	</tr>
	</table>
	</div>

	<div id="addrelation" style="display:none;">
		<h2>Ajouter une relation</h2>
		<div>
			<label>Type : <select name="t_relationship_type">
			<option value="">Choisissez un type</option>
			<option value="belongs-to">Belongs to (À la clé primaire de sa relation dans sa table, appartient à 1 objet relié)</option>
			<option value="has-one">Has one (À sa clé primaire dans un seul enregistrement d'une autre table, qui est "belongs to" par rapport à elle, a 1 seul objet relié)</option>
			<option value="has-many">Has many (À sa clé primaire dans plusieurs enregistrement d'une autre table, qui est "belongs to" par rapport à elle, a plusieurs objets reliés)</option>
			<option value="many-to-many">May to many</option>
			</select></label>
		</div>
		<div>
			<label>Model : <input type="text" name="t_relationship_model"></label>
		</div>
		<div>
			<label id="relationship_table">Table : <select name="t_relationship_table">
			<option value="">Choisissez une table</option>
<?php
	foreach ($tables as $item) {
?>
			<option value="<?= $item ?>"><?= $item ?></option>
<?php
	}
?>
			</select></label>
		</div>
		<div>
			<label>Nom : <input type="text" name="t_relationship_name"></label>
		</div>
		<div><label id="foreignkey" style="display:none;">Clé étrangère : <select name="t_relationship_foreignkey">
			<option value="">Choisissez une clé étrangère</option>
		</select></label></div>
		<div id="relationship_through" style="display:none;">
			<label id="relationship_table_through">Table intermédiaire : <select name="t_relationship_table_through">
			<option value="">Choisissez une table</option>
<?php
	foreach ($tables as $item) {
?>
			<option value="<?= $item ?>"><?= $item ?></option>
<?php
	}
?>
			</select></label>
			<div><label id="foreignkey_through_from">Clé étrangère intermédaire de votre table : <select name="t_relationship_foreignkey_through_from">
				<option value="">Choisissez la clé étrangère</option>
			</select></label></div>
			<div><label id="foreignkey_through_to">Clé étrangère intermédaire de la table liée : <select name="t_relationship_foreignkey_through_to">
				<option value="">Choisissez la clé étrangère</option>
			</select></label></div>
		</div>
		<div><button type="button" id="submitadd">Ajouter</button></div>
	</div>
	<br />

	<div align="center" id="submit"><button>Générer le model</button></div>
</form>
<script type="text/javascript">
	var model = {
		table_props        : {},
		relationship_through_props : {},
		relationship_props : {},
		request_ajax_model   : false,
		setRelationshipModel : function() {
			var input = $('input[name=t_relationship_model]');
			if (input.val()) {
				$.ajax({
					url      : '<?= $url.'/json_model' ?>',
					data     : {
						model : input.val(),
						namespace : $('input[name=namespace]').val(),
						subdir : $('input[name=subdir]').val()
					},
					dataType : 'text',
					success  : function(data, textSatus, jqXHR) {
						if (data && $('select[name=t_relationship_table] option[value="' + data + '"]').size()) {
							$('select[name=t_relationship_table] option[value="' + data + '"]').attr('selected', true);
							$('#relationship_table').hide();
							model.setRelationshipTable();
						} else {
							$('#relationship_table').show();
						}
					}
				});
			} else {
				model.setRelationshipTable();
				$('#relationship_table').show();
			}
		},
		setRelationshipTable : function() {
			var table = $('select[name=t_relationship_table]').val();
			if (table) {
				$.ajax({
					url      : '<?= $url.'/json_table' ?>',
					data     : {
						table     : table
					},
					dataType : 'json',
					success  : function(data, textSatus, jqXHR) {
						model.relationship_props = data;
						model.foreignkey();
					}
				});
			} else {
				model.relationship_props = {};
				this.foreignkey();
			}
		},
		setRelationshipTableThrough : function() {
			var table = $('select[name=t_relationship_table_through]').val();
			if (table) {
				$.ajax({
					url      : '<?= $url.'/json_table' ?>',
					data     : {
						table     : table
					},
					dataType : 'json',
					success  : function(data, textSatus, jqXHR) {
						model.relationship_through_props = data;
						model.foreignkey();
					}
				});
			} else {
				model.relationship_through_props = {};
				this.foreignkey();
			}
		},
		foreignkey           : function() {
			var relationship_type = $('select[name=t_relationship_type]');
			if (relationship_type.val()) {
				if (relationship_type.val() == 'many-to-many') {
					$('#relationship_through').show();
					$('#foreignkey').hide();

					$('select[name=t_relationship_foreignkey_through_from] option:gt(0)').remove();
					$('select[name=t_relationship_foreignkey_through_to] option:gt(0)').remove();
					$.each(this.relationship_through_props.columns || {}, function(column, type) {
						$('select[name=t_relationship_foreignkey_through_from]').append('<option value="' + column + '">' + column + '</option>');
						$('select[name=t_relationship_foreignkey_through_to]').append('<option value="' + column + '">' + column + '</option>');
					});
				} else {
					$('#relationship_through').hide();

					var value = $('select[name=t_relationship_foreignkey]').val();
					$('#foreignkey').show();
					$('select[name=t_relationship_foreignkey] option:gt(0)').remove();
					var columns = this.table_props.columns ? this.table_props.columns : {};
					var primary = this.table_props.primary ? this.table_props.primary : '';
					if (relationship_type.val() == 'has-many' || relationship_type.val() == 'has-one') {
						columns = this.relationship_props.columns ? this.relationship_props.columns : {};
						primary = this.relationship_props.primary ? this.relationship_props.primary : '';
					}
					$.each(columns, function(column, type) {
						if (primary != column) {
							$('select[name=t_relationship_foreignkey]').append('<option value="' + column + '">' + column + '</option>');
						}
					});
					$('select[name=t_relationship_foreignkey]').val(value);
				}
			} else {
				$('#foreignkey').hide();
				$('#relationship_through').hide();
			}
		}
	};
	$(document).ready(function() {
		$('select[name=table]').change(function() {
			if ($(this).val()) {
				$.ajax({
					url      : '<?= $url.'/json_table' ?>',
					data     : {
						table : $(this).val()
					},
					dataType : 'json',
					success  : function(data, textSatus, jqXHR) {
						model.table_props = data;
						$('#submit').show();
						$('#addrelation').show(function() {
							model.setRelationshipModel();
						});
					}
				});
			} else {
				$('#addrelation').hide();
				$('#submit').hide();
			}
		});
		$('select[name=table]').change();

		$('input[name=t_relationship_model]').bind('change keypress keydown keyup', function() {
			if (model.request_ajax_model) {
				clearTimeout(model.request_ajax_model);
			}
			model.request_ajax_model = setTimeout(model.setRelationshipModel, 1000);
		});

		$('select[name=t_relationship_table]').change(function() {
			model.setRelationshipTable();
		});

		$('select[name=t_relationship_table_through]').change(function() {
			model.setRelationshipTableThrough();
		});

		$('select[name=t_relationship_type]').change(function() {
			model.foreignkey();
		});

		$('#submitadd').click(function() {
			if (!$('select[name=t_relationship_table]').val()) {
				alert('Vous devez choisir la table ou le model de votre relation');
				return false;
			}
			if (!$('select[name=t_relationship_type]').val()) {
				alert('Vous devez choisir un type pour votre relation');
				return false;
			}
			var relation = '';
			var table = $('select[name=t_relationship_table] option:selected').val();
			var name = $('input[name=t_relationship_name]').val();
			var classname = $('input[name=t_relationship_model]').val();
			if ($('select[name=t_relationship_type] option:selected').val() == 'has-many') {
			    if (!$('select[name=t_relationship_foreignkey] option:selected').val()) {
    				if (!$('select[name=t_relationship_foreignkey] option[value=' + model.relationship_props.prefixe + model.table_props.primary + ']').size()) {
    					alert('Vous devez choisir une clé étrangère pour votre relation');
    					return false;
    				} else if (!$('select[name=t_relationship_foreignkey] option:selected').val()) {
    					$('select[name=t_relationship_foreignkey] option[value=' + model.relationship_props.prefixe + model.table_props.primary + ']').attr('selected', 'selected');
    				}
			    }
				relation  = $('select[name=table] option:selected').val() + '.' + model.table_props.primary + ' = ' + table + '.' + $('select[name=t_relationship_foreignkey] option:selected').val();
			} else if ($('select[name=t_relationship_type] option:selected').val() == 'has-one') {
                if (!$('select[name=t_relationship_foreignkey] option:selected').val()) {
                    if (!$('select[name=t_relationship_foreignkey] option[value=' + model.relationship_props.prefixe + model.table_props.primary + ']').size()) {
    					alert('Vous devez choisir une clé étrangère pour votre relation');
    					return false;
    				} else if (!$('select[name=t_relationship_foreignkey] option:selected').val()) {
    					$('select[name=t_relationship_foreignkey] option[value=' + model.relationship_props.prefixe + model.table_props.primary + ']').attr('selected', 'selected');
    				}
                }
				relation  = $('select[name=table] option:selected').val() + '.' + model.table_props.primary + ' = ' + table + '.' + $('select[name=t_relationship_foreignkey] option:selected').val();
			} else if ($('select[name=t_relationship_type] option:selected').val() == 'belongs-to') {
			    if (!$('select[name=t_relationship_foreignkey] option:selected').val()) {
    				if (!$('select[name=t_relationship_foreignkey] option[value=' + model.table_props.prefixe + model.relationship_props.primary + ']').size()) {
    					alert('Vous devez choisir une clé étrangère pour votre relation');
    					return false;
    				} else if (!$('select[name=t_relationship_foreignkey] option:selected').val()) {
    					$('select[name=t_relationship_foreignkey] option[value=' + model.table_props.prefixe + model.relationship_props.primary + ']').attr('selected', 'selected');
    				}
			    }
				relation  = $('select[name=table] option:selected').val() + '.' + $('select[name=t_relationship_foreignkey] option:selected').val() + ' = ' + table + '.' + model.relationship_props.primary;
			}

			relation += '<input type="hidden" name="relationships_table[]" value="' + table + '" />';
			relation += '<input type="hidden" name="relationships_foreignkey[]" value="' + $('select[name=t_relationship_foreignkey] option:selected').val() + '" />';
			relation += '<input type="hidden" name="relationship_table_through[]" value="" />';
			relation += '<input type="hidden" name="relationship_foreignkey_through_from[]" value="" />';
			relation += '<input type="hidden" name="relationship_foreignkey_through_to[]" value="" />';
			if ($('select[name=t_relationship_type]').val() == 'many-to-many') {
				if (!$('select[name=t_relationship_table_through]').val()) {
					alert('Vous devez choisir la table intermédiaire de votre relation');
					return false;
				}
				if (!$('select[name=t_relationship_foreignkey_through_from]').val()) {
					if (!$('select[name=t_relationship_foreignkey_through_from] option[value=' + model.relationship_through_props.prefixe + model.table_props.primary + ']').size()) {
						alert('Vous devez choisir une clé étrangère intermédaire de votre table pour votre relation');
						return false;
					} else {
						$('select[name=t_relationship_foreignkey_through_from] option[value=' + model.relationship_through_props.prefixe + model.table_props.primary + ']').attr('selected', 'selected');
					}
				}
				if (!$('select[name=t_relationship_foreignkey_through_to]').val()) {
					if (!$('select[name=t_relationship_foreignkey_through_to] option[value=' + model.relationship_through_props.prefixe + model.relationship_props.primary + ']').size()) {
						alert('Vous devez choisir une clé étrangère intermédaire de la table liée pour votre relation');
						return false;
					} else {
						$('select[name=t_relationship_foreignkey_through_to] option[value=' + model.relationship_through_props.prefixe + model.relationship_props.primary + ']').attr('selected', 'selected');
					}
				}

				relation  = $('select[name=table] option:selected').val() + '.' + model.table_props.primary + ' = ' + $('select[name=t_relationship_table_through]').val() + '.' + $('select[name=t_relationship_foreignkey_through_from] option:selected').val();
				relation += ' AND ' + $('select[name=t_relationship_table_through]').val() + '.' + $('select[name=t_relationship_foreignkey_through_to] option:selected').val() + ' = ' + table + '.' + model.relationship_props.primary;
				relation += '<input type="hidden" name="relationships_table[]" value="' + table + '" />';
				relation += '<input type="hidden" name="relationships_foreignkey[]" value="" />';
				relation += '<input type="hidden" name="relationship_table_through[]" value="' + $('select[name=t_relationship_table_through]').val() + '" />';
				relation += '<input type="hidden" name="relationship_foreignkey_through_from[]" value="' + $('select[name=t_relationship_foreignkey_through_from] option:selected').val() + '" />';
				relation += '<input type="hidden" name="relationship_foreignkey_through_to[]" value="' + $('select[name=t_relationship_foreignkey_through_to] option:selected').val() + '" />';
			}

			var tr = $('<tr></tr>');
			tr.append('<td>' + name + '<input type="hidden" name="relationships_name[]" value="' + name + '" /></td>');
			tr.append('<td>' + classname + '<input type="hidden" name="relationships_model[]" value="' + classname + '" /></td>');
			tr.append('<td>' + $('select[name=t_relationship_type] option:selected').val() + '<input type="hidden" name="relationships_type[]" value="' + $('select[name=t_relationship_type] option:selected').val() + '" /></td>');
			tr.append('<td>' + relation + '</td>');
			tr.append('<td align="center"><button type="button" class="delrelation">Supprimer</button></td>');
			tr.appendTo('#relationships table');
			$('#relationships').show();
			return true;
		});
		$('button.delrelation').live('click', function() {
			$(this).parents('tr').remove();
		});
	});
</script>
