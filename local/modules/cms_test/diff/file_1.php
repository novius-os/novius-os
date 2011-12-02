<?php


$fieldset = \Fieldset::forge(uniqid(), array(
	'inline_errors' => true,
));
\Config::load('cms::user/form', true);

$fieldset->add_widgets(\Config::get('cms::user/form.fields', array()), array(
	'action' => 'add',
));

$fieldset->validation()->set_message('required', 'This field is required');
$fieldset->validation()->set_message('valid_date', 'The entered date is invalid.');

$fieldset->add('submit', '', array(
	'type' => 'submit',
	'value' => 'Save',
));

if (\Input::method() == 'POST') {
	$fieldset->repopulate();

	if ($fieldset->validation()->run($fieldset->value())) {
		$user = new Model_User();
		foreach ($fieldset->field() as $f) {
			if (strtolower(\Inflector::denamespace(get_class($f))) == 'widget_empty' || $f->name == 'submit') {
				continue;
			}
			$user->{$f->get_name()} = $f->get_value();
		}
		$user->save();
		\Response::redirect('admin/user/form/add_success');
	}
}