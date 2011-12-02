
*NOVIUS OS - Web OS for digital communication*

*@copyright  2011 Novius<br />
@license    GNU Affero General Public License v3 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0.html<br />
@link http://www.novius-os.org*


<style type="text/css">
code {
    white-space: pre;
}
</style>

 Documentation - Form generation
=================================

*[Back to index](/admin/doc)*

 Example config file
---------------------

```
array (
    'user_id' => array (
        'label' => 'ID',
        'add' => false,
        'widget' => 'text',
    ),
    'user_fullname' => array (
        'label' => 'Full name',
        'widget' => '',
        'validation' => array(
            'required',
        ),
    ),
    'user_email' => array(
        'label' => 'Email',
        'validation' => array(
            'required',
            'valid_email',
        ),
    ),
    'user_password' => array (
        'label' => 'Password',
        'widget' => 'password',
        'validation' => array(
            'required',
            'min_length' => array(6),
        ),
    ),
    'user_last_connection' => array (
        'label' => 'Last login',
        'add' => false,
        'widget' => 'date_select',
        'attributes' => array(
            'readonly' => true,
            'date_format' => 'eu_full',
        ),
    ),
);
```

The fields user_fullname and user_email have no widget specified: a standard input will be generated.


 Available options for the fields
----------------------------------

* *add*: if set to false, won't be displayed in the add form
* *edit*: if set to false, won't be displayed in the edit form
* *widget*: name of the widget to use. None to use standard input field.

Inherited from Fuel:

* *validation*: rules to ensure entered data is correct
* *label*: text to display next to the field/widget

 Available widgets
-------------------

### text

* Value is displayed as plain text (no input tag)


### password

* Adds 2 sets of {label + field} in the fielset:

 * A first standard input password tag
 * A second one to validate the value

*Validation done*

* match: ensures the two entered passwords are identical (case-sensitive)


### date_select

* Creates 3 inputs in the same label:

 * Day: a text field 
 * Month: a select field 
 * Year: a text field

*Attributes*

* readonly: will show the value as plain-text instead of 3 fields
* date_format: used when readonly is *true*

*Validation done*

* valid_date: ensures the day entered exists in the calendar