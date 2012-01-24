<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

    $id = uniqid('temp_');
?>
<div id="<?= $id ?>" style="display:none;"><?= str_replace(array('xxxbeginxxx', 'xxxendxxx'), array($date_begin, $date_end), $label_custom) ?></div>
<script type="text/javascript">
require([
		'jquery-nos'
	], function( $, undefined ) {
		$(function() {
			var label_custom = $('#<?= $id ?>').removeAttr('id')
                    .css({
                        display : 'inline-block',
                        whiteSpace : 'nowrap'
                    }).hide(),
                inspector = $('<table></table>').insertAfter(label_custom),
                parent = inspector.parent().bind({
                        inspectorResize: function() {
                            label_custom.appendTo(parent);
                            inspector.nosgrid('destroy')
                                .empty();
                            init();
                        }
                    }),
                inspectorData = parent.data('inspector'),
				dates = label_custom.find(':input').datepicker('option', 'onSelect', function( selectedDate ) {
						var option = this === label_custom.find(':input:first')[0] ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							begin = label_custom.find(':input:first').val(),
						    end = label_custom.find(':input:last').val(),
						    label = "<?= $label_custom ?>";

						var date = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );

						if (begin || end) {
							if (begin) {
								label = label.replace('xxxbeginxxx', begin);
							} else {
								label = 'Until ' + end;
							}
							if (end) {
								label = label.replace('xxxendxxx', end);
							} else {
								label = 'Since ' + begin;
							}
                            if ($.isFunction(inspectorData.selectionChanged)) {
                                inspectorData.selectionChanged(begin + '|' + end, label);
                            }
						}
					}),
                rendered = false,
				init = function() {
					inspector.css({
							height : '100%',
							width : '100%'
						})
						.nosgrid({
							columnsAutogenerationMode : 'none',
							scrollMode : 'auto',
							staticRowIndex : 0,
							showGroupArea : false,
							columns : [
								{
			                        dataKey : 'group',
			                        groupInfo: {
			        					groupSingleRow : false,
			                            position: "header",
			                            outlineMode: "startCollapsed",
			                            headerText: "<b>{0}</b>"
			                        },
			                        visible : false
			                    },
			                    {
				                    headerText : inspectorData.label,
									cellFormatter: function (args) {
										if ($.isPlainObject(args.row.data) && args.row.data.value === 'custom') {
											args.$container.css({
													'white-space' : 'normal',
													'padding-left' : '10px'
												});
											$('<span></span>').text(args.row.data.title)
												.css({
														'white-space' : 'nowrap',
														'margin-right' : '10px'
													})
												.appendTo(args.$container);
											label_custom.appendTo(args.$container);
											return true;
										} else {
											args.$container.css('padding-left', '30px');
										}
									},
			                        dataKey : 'title'
				                },
				                {
			                        visible : false
				                }
							],
							data: <?= $content ?>,
							currentCellChanged: function (e) {
								var row = $(e.target).nosgrid("currentCell").row(),
									data = row ? row.data : false;

								if (data && rendered) {
									if (data.value !== 'custom') {
										label_custom.hide();
									}
									if (data.value === 'custom') {
										label_custom.show();
									} else {
                                        inspectorData.selectionChanged(data.value, data.title);
									}
								}
								inspector.nosgrid("currentCell", -1, -1);
							},
							rendering : function() {
								rendered = false;
							},
							rendered : function() {
								rendered = true;
								inspector.css('height', 'auto');
							}
						});
				};

			init();
		});
	});
</script>