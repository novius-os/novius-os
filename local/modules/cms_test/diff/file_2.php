<?php

        
$fieldset = \Fieldset::forge(uniqid(), array(
	'inline_errors' => true,
));
$user = Model_User::find($id);
\Config::load('cms::user/form', true);

$fieldset->form_name('edit_user');
$fieldset->add_widgets(\Config::get('cms::user/form.fields', array()), array(
	'action' => 'edit',
));

$fieldset->validation()->set_message('required', 'This field is required');
$fieldset->validation()->set_message('valid_date', 'The entered date is invalid.');

$fieldset->add('submit', '', array(
	'type' => 'submit',
	'value' => 'Save',
));

$fieldset->populate($user);
if (\Input::method() == 'POST' && \Input::post('form_name') == 'edit_user') {
	$fieldset->repopulate();

	if ($fieldset->validation()->run($fieldset->value())) {
		foreach ($fieldset->field() as $f) {
			if (strtolower(\Inflector::denamespace(get_class($f))) == 'widget_empty' || $f->name == 'submit') {
				continue;
			}
			try {
				$user->{$f->get_name()} = $f->get_value();
			} catch (\Exception $e) {}
		}
		$user->save();
		\Response::redirect('admin/user/form/edit_success');
	}   
}